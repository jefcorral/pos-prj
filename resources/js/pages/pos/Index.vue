<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import {
    Banknote,
    CreditCard,
    Pause,
    Play,
    Plus,
    Minus,
    ScanBarcode,
    Search,
    Trash2,
    X,
} from '@lucide/vue';
import { computed, onMounted, ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useMoney } from '@/composables/useMoney';
import AppLayout from '@/layouts/AppLayout.vue';
import { checkout, hold, products as searchProducts } from '@/routes/pos';
import { open as openShift } from '@/routes/shifts';
import { receipt as saleReceipt } from '@/routes/sales';
import type { ActiveShift } from '@/types';

type PosProduct = {
    id: number;
    name: string;
    sku: string | null;
    barcode: string | null;
    price: number;
    tax_rate: number;
    tax_type: string | null;
    image: string | null;
    stock: number;
    track_stock: boolean;
    variants: {
        id: number;
        name: string;
        price: number;
        barcode: string | null;
    }[];
};

type CartLine = {
    key: string;
    product_id: number;
    product_variant_id: number | null;
    name: string;
    price: number;
    tax_rate: number;
    tax_type: string | null;
    quantity: number;
    discount: number;
    stock: number;
    track_stock: boolean;
};

type HeldSale = {
    id: number;
    number: string;
    total: string;
    note: string | null;
    items: {
        product_id: number;
        product_variant_id: number | null;
        name: string;
        quantity: string;
        unit_price: string;
        discount: string;
    }[];
};

const props = defineProps<{
    categories: { id: number; name: string }[];
    customers: { id: number; name: string }[];
    heldSales: HeldSale[];
    shift: ActiveShift;
}>();

const page = usePage();
const { format } = useMoney();

const search = ref('');
const categoryId = ref('all');
const products = ref<PosProduct[]>([]);
const cart = ref<CartLine[]>([]);
const customerId = ref<string>('none');
const discountType = ref<'none' | 'percent' | 'fixed'>('none');
const discountValue = ref(0);
const heldSaleId = ref<number | null>(null);
const loading = ref(false);
const searchInput = ref<HTMLInputElement | null>(null);
const paymentOpen = ref(false);
const receiptOpen = ref(false);
const shiftOpen = ref(false);
const receiptData = ref<Record<string, any> | null>(null);

const paymentForm = useForm<{
    items: any[];
    payments: {
        method: string;
        amount: number | undefined;
        reference: string;
    }[];
    discount: { type: string; value: number } | null;
    customer_id: number | null;
    held_sale_id: number | null;
}>({
    items: [],
    payments: [{ method: 'cash', amount: undefined, reference: '' }],
    discount: null,
    customer_id: null,
    held_sale_id: null,
});

const shiftForm = useForm({ opening_cash: 0, note: '' });

async function fetchProducts() {
    loading.value = true;
    try {
        const params = new URLSearchParams();
        if (search.value) params.set('search', search.value);
        if (categoryId.value !== 'all')
            params.set('category_id', categoryId.value);
        const res = await fetch(`${searchProducts.url()}?${params}`, {
            headers: { Accept: 'application/json' },
        });
        products.value = await res.json();
    } finally {
        loading.value = false;
    }
}

let debounce: ReturnType<typeof setTimeout>;
watch([search, categoryId], () => {
    clearTimeout(debounce);
    debounce = setTimeout(fetchProducts, 200);
});

function addToCart(product: PosProduct, variantId: number | null = null) {
    const variant = variantId
        ? product.variants.find((v) => v.id === variantId)
        : null;
    const key = `${product.id}:${variantId ?? ''}`;
    const existing = cart.value.find((l) => l.key === key);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.value.push({
            key,
            product_id: product.id,
            product_variant_id: variantId,
            name: variant ? `${product.name} (${variant.name})` : product.name,
            price: variant?.price ?? product.price,
            tax_rate: product.tax_rate,
            tax_type: product.tax_type,
            quantity: 1,
            discount: 0,
            stock: product.stock,
            track_stock: product.track_stock,
        });
    }
}

function onScanEnter() {
    // Barcode scanners send the code then Enter
    const exact = products.value.find(
        (p) =>
            p.barcode === search.value ||
            p.variants.some((v) => v.barcode === search.value),
    );
    if (exact) {
        const variant =
            exact.variants.find((v) => v.barcode === search.value) ?? null;
        addToCart(exact, variant?.id ?? null);
        search.value = '';
    } else if (products.value.length === 1) {
        addToCart(products.value[0]);
        search.value = '';
    }
}

function setQty(line: CartLine, delta: number) {
    line.quantity = Math.max(0, line.quantity + delta);
    if (line.quantity === 0) {
        cart.value = cart.value.filter((l) => l.key !== line.key);
    }
}

const subtotal = computed(() =>
    cart.value.reduce((s, l) => s + l.price * l.quantity - l.discount, 0),
);
const discountTotal = computed(() => {
    if (discountType.value === 'percent') {
        return Math.min(
            subtotal.value * (discountValue.value / 100),
            subtotal.value,
        );
    }
    if (discountType.value === 'fixed') {
        return Math.min(discountValue.value, subtotal.value);
    }
    return 0;
});
const taxTotal = computed(() =>
    cart.value.reduce((s, l) => {
        const gross = l.price * l.quantity - l.discount;
        const share = subtotal.value > 0 ? gross / subtotal.value : 0;
        const taxable = gross - discountTotal.value * share;
        if (!l.tax_rate) return s;
        return (
            s +
            (l.tax_type === 'inclusive'
                ? taxable - taxable / (1 + l.tax_rate / 100)
                : taxable * (l.tax_rate / 100))
        );
    }, 0),
);
const total = computed(() =>
    Math.max(0, subtotal.value - discountTotal.value + cartTaxExclusive.value),
);
const cartTaxExclusive = computed(() =>
    cart.value.reduce((s, l) => {
        if (!l.tax_rate || l.tax_type === 'inclusive') return s;
        const gross = l.price * l.quantity - l.discount;
        const share = subtotal.value > 0 ? gross / subtotal.value : 0;
        const taxable = gross - discountTotal.value * share;
        return s + taxable * (l.tax_rate / 100);
    }, 0),
);

const paidTotal = computed(() =>
    paymentForm.payments.reduce((s, p) => s + (Number(p.amount) || 0), 0),
);
const change = computed(() => Math.max(0, paidTotal.value - total.value));

function addPaymentRow() {
    paymentForm.payments.push({
        method: 'card',
        amount: undefined,
        reference: '',
    });
}

function openPayment() {
    paymentForm.payments = [
        {
            method: 'cash',
            amount: Math.round(total.value * 100) / 100,
            reference: '',
        },
    ];
    paymentOpen.value = true;
}

function submitCheckout() {
    paymentForm
        .transform(() => ({
            items: cart.value.map((l) => ({
                product_id: l.product_id,
                product_variant_id: l.product_variant_id,
                quantity: l.quantity,
                discount: l.discount,
            })),
            payments: paymentForm.payments
                .filter((p) => Number(p.amount) > 0)
                .map((p) => ({
                    method: p.method,
                    amount: Number(p.amount),
                    reference: p.reference || null,
                })),
            discount:
                discountType.value !== 'none' && discountValue.value > 0
                    ? { type: discountType.value, value: discountValue.value }
                    : null,
            customer_id:
                customerId.value !== 'none' ? Number(customerId.value) : null,
            held_sale_id: heldSaleId.value,
        }))
        .post(checkout.url(), {
            preserveScroll: true,
            onSuccess: () => {
                paymentOpen.value = false;
                clearCart();
                const saleId = (
                    page.props.flash as { completed_sale?: number } | undefined
                )?.completed_sale;
                if (saleId) showReceipt(saleId);
            },
        });
}

async function showReceipt(saleId: number) {
    const res = await fetch(saleReceipt.url(saleId), {
        headers: { Accept: 'application/json' },
    });
    receiptData.value = await res.json();
    receiptOpen.value = true;
}

function holdCart() {
    if (!cart.value.length) return;
    router.post(
        hold.url(),
        {
            items: cart.value.map((l) => ({
                product_id: l.product_id,
                product_variant_id: l.product_variant_id,
                quantity: l.quantity,
                discount: l.discount,
            })),
            discount:
                discountType.value !== 'none' && discountValue.value > 0
                    ? { type: discountType.value, value: discountValue.value }
                    : null,
            customer_id:
                customerId.value !== 'none' ? Number(customerId.value) : null,
            held_sale_id: heldSaleId.value,
        },
        { preserveScroll: true, onSuccess: () => clearCart() },
    );
}

function resumeSale(sale: HeldSale) {
    cart.value = sale.items.map((i) => ({
        key: `${i.product_id}:${i.product_variant_id ?? ''}`,
        product_id: i.product_id,
        product_variant_id: i.product_variant_id,
        name: i.name,
        price: Number(i.unit_price),
        tax_rate: 0,
        tax_type: null,
        quantity: Number(i.quantity),
        discount: Number(i.discount),
        stock: 0,
        track_stock: false,
    }));
    heldSaleId.value = sale.id;
}

function clearCart() {
    cart.value = [];
    discountType.value = 'none';
    discountValue.value = 0;
    customerId.value = 'none';
    heldSaleId.value = null;
}

function openShiftSubmit() {
    shiftForm.post(openShift.url(), {
        onSuccess: () => (shiftOpen.value = false),
    });
}

function printReceipt() {
    window.print();
}

onMounted(() => {
    fetchProducts();
    searchInput.value?.focus();
    const saleId = (page.props.flash as { completed_sale?: number } | undefined)
        ?.completed_sale;
    if (saleId) showReceipt(saleId);
});

const paymentMethods = [
    { value: 'cash', label: 'Cash', icon: Banknote },
    { value: 'card', label: 'Card', icon: CreditCard },
    { value: 'gcash', label: 'GCash', icon: Banknote },
    { value: 'maya', label: 'Maya', icon: Banknote },
    { value: 'bank_transfer', label: 'Bank Transfer', icon: Banknote },
    { value: 'other', label: 'Other', icon: Banknote },
];
</script>

<template>
    <Head title="POS" />
    <AppLayout>
        <div class="flex h-full flex-col gap-3 p-4">
            <!-- Top bar -->
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <ScanBarcode
                        class="text-muted-foreground absolute top-2.5 left-3 h-5 w-5"
                    />
                    <Input
                        ref="searchInput"
                        v-model="search"
                        placeholder="Scan barcode or search products… (Enter to add)"
                        class="h-10 pl-10 text-base"
                        @keydown.enter="onScanEnter"
                    />
                </div>
                <Select v-model="customerId">
                    <SelectTrigger class="w-48">
                        <SelectValue placeholder="Walk-in customer" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="none">Walk-in customer</SelectItem>
                        <SelectItem
                            v-for="c in customers"
                            :key="c.id"
                            :value="String(c.id)"
                        >
                            {{ c.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <Badge v-if="shift" variant="secondary" class="h-8 px-3">
                    Shift #{{ shift.id }} open
                </Badge>
                <Button v-else variant="outline" @click="shiftOpen = true">
                    Open Shift
                </Button>
            </div>

            <!-- Held sales -->
            <div
                v-if="heldSales.length"
                class="flex items-center gap-2 overflow-x-auto pb-1"
            >
                <span class="text-muted-foreground text-xs whitespace-nowrap"
                    >Held:</span
                >
                <Button
                    v-for="h in heldSales"
                    :key="h.id"
                    size="sm"
                    variant="outline"
                    @click="resumeSale(h)"
                >
                    <Play class="mr-1 h-3 w-3" /> {{ h.number }} ·
                    {{ format(h.total) }}
                </Button>
            </div>

            <div class="grid flex-1 grid-cols-5 gap-4">
                <!-- Products -->
                <div class="col-span-3 flex flex-col gap-3">
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        <Button
                            size="sm"
                            :variant="
                                categoryId === 'all' ? 'default' : 'outline'
                            "
                            @click="categoryId = 'all'"
                            >All</Button
                        >
                        <Button
                            v-for="c in categories"
                            :key="c.id"
                            size="sm"
                            :variant="
                                categoryId === String(c.id)
                                    ? 'default'
                                    : 'outline'
                            "
                            @click="categoryId = String(c.id)"
                            >{{ c.name }}</Button
                        >
                    </div>
                    <div
                        class="grid flex-1 auto-rows-min grid-cols-3 gap-2 overflow-y-auto xl:grid-cols-4"
                    >
                        <button
                            v-for="p in products"
                            :key="p.id"
                            class="bg-card hover:border-primary flex flex-col rounded-lg border p-3 text-left transition hover:shadow-sm"
                            @click="addToCart(p)"
                        >
                            <span class="line-clamp-2 text-sm font-medium">{{
                                p.name
                            }}</span>
                            <span class="mt-auto pt-2 text-sm font-bold">{{
                                format(p.price)
                            }}</span>
                            <span
                                class="text-xs"
                                :class="
                                    p.track_stock && p.stock <= 0
                                        ? 'text-destructive'
                                        : 'text-muted-foreground'
                                "
                            >
                                {{
                                    p.track_stock ? `${p.stock} in stock` : '—'
                                }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Cart -->
                <div class="bg-card col-span-2 flex flex-col rounded-lg border">
                    <div class="border-b p-3 font-semibold">
                        Cart
                        <Badge v-if="heldSaleId" class="ml-2" variant="outline"
                            >Resuming held sale</Badge
                        >
                    </div>
                    <div class="flex-1 overflow-y-auto p-2">
                        <div
                            v-for="line in cart"
                            :key="line.key"
                            class="hover:bg-muted mb-1 flex items-center gap-2 rounded-md p-2"
                        >
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium">
                                    {{ line.name }}
                                </div>
                                <div class="text-muted-foreground text-xs">
                                    {{ format(line.price) }}
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <Button
                                    size="icon"
                                    variant="outline"
                                    class="h-6 w-6"
                                    @click="setQty(line, -1)"
                                    ><Minus class="h-3 w-3"
                                /></Button>
                                <input
                                    v-model.number="line.quantity"
                                    type="number"
                                    min="0"
                                    class="h-6 w-12 rounded border text-center text-sm"
                                />
                                <Button
                                    size="icon"
                                    variant="outline"
                                    class="h-6 w-6"
                                    @click="setQty(line, 1)"
                                    ><Plus class="h-3 w-3"
                                /></Button>
                            </div>
                            <div class="w-20 text-right text-sm font-medium">
                                {{
                                    format(
                                        line.price * line.quantity -
                                            line.discount,
                                    )
                                }}
                            </div>
                            <Button
                                size="icon"
                                variant="ghost"
                                class="text-destructive h-6 w-6"
                                @click="
                                    cart = cart.filter(
                                        (l) => l.key !== line.key,
                                    )
                                "
                                ><X class="h-3 w-3"
                            /></Button>
                        </div>
                        <p
                            v-if="!cart.length"
                            class="text-muted-foreground p-6 text-center text-sm"
                        >
                            Scan a barcode or click a product to start
                        </p>
                    </div>

                    <!-- Totals -->
                    <div class="space-y-1 border-t p-3 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal</span
                            ><span>{{ format(subtotal) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                Discount
                                <Select v-model="discountType">
                                    <SelectTrigger class="h-6 w-24 text-xs">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="none"
                                            >None</SelectItem
                                        >
                                        <SelectItem value="percent"
                                            >%</SelectItem
                                        >
                                        <SelectItem value="fixed"
                                            >Fixed</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                                <Input
                                    v-if="discountType !== 'none'"
                                    v-model.number="discountValue"
                                    type="number"
                                    min="0"
                                    class="h-6 w-20 text-xs"
                                />
                            </span>
                            <span>-{{ format(discountTotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tax</span><span>{{ format(taxTotal) }}</span>
                        </div>
                        <div
                            class="flex justify-between border-t pt-2 text-xl font-bold"
                        >
                            <span>TOTAL</span><span>{{ format(total) }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2 p-3">
                        <Button
                            variant="outline"
                            class="flex-1"
                            :disabled="!cart.length"
                            @click="holdCart"
                            ><Pause class="mr-1 h-4 w-4" /> Hold</Button
                        >
                        <Button
                            variant="outline"
                            class="flex-1"
                            :disabled="!cart.length"
                            @click="clearCart"
                            ><Trash2 class="mr-1 h-4 w-4" /> Clear</Button
                        >
                        <Button
                            class="flex-1"
                            size="lg"
                            :disabled="!cart.length || paymentForm.processing"
                            @click="openPayment"
                            >Charge {{ format(total) }}</Button
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment dialog -->
        <Dialog v-model:open="paymentOpen">
            <DialogContent class="max-w-lg">
                <DialogHeader>
                    <DialogTitle>Payment — {{ format(total) }}</DialogTitle>
                </DialogHeader>
                <div class="space-y-3">
                    <div
                        v-for="(p, i) in paymentForm.payments"
                        :key="i"
                        class="flex items-center gap-2"
                    >
                        <Select v-model="p.method">
                            <SelectTrigger class="w-40">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="m in paymentMethods"
                                    :key="m.value"
                                    :value="m.value"
                                    >{{ m.label }}</SelectItem
                                >
                            </SelectContent>
                        </Select>
                        <Input
                            v-model.number="p.amount"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="Amount"
                            class="flex-1"
                        />
                        <Input
                            v-if="p.method !== 'cash'"
                            v-model="p.reference"
                            placeholder="Reference #"
                            class="w-36"
                        />
                        <Button
                            size="icon"
                            variant="ghost"
                            @click="paymentForm.payments.splice(i, 1)"
                            ><X class="h-4 w-4"
                        /></Button>
                    </div>
                    <Button variant="outline" size="sm" @click="addPaymentRow"
                        ><Plus class="mr-1 h-4 w-4" /> Split payment</Button
                    >
                    <div
                        class="flex justify-between border-t pt-3 text-lg font-bold"
                    >
                        <span>Change</span>
                        <span>{{ format(change) }}</span>
                    </div>
                    <InputError :message="paymentForm.errors.payments" />
                    <InputError :message="paymentForm.errors.items" />
                </div>
                <DialogFooter>
                    <Button
                        class="w-full"
                        size="lg"
                        :disabled="
                            paymentForm.processing || paidTotal < total - 0.001
                        "
                        @click="submitCheckout"
                    >
                        Complete Sale ({{ format(paidTotal) }})
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Receipt dialog -->
        <Dialog v-model:open="receiptOpen">
            <DialogContent class="max-w-sm">
                <DialogHeader>
                    <DialogTitle>Receipt</DialogTitle>
                </DialogHeader>
                <div
                    v-if="receiptData"
                    id="receipt"
                    class="font-mono text-xs whitespace-pre-wrap"
                >
                    <div class="text-center">
                        <div class="text-sm font-bold">
                            {{ receiptData.payload?.branch?.name ?? 'Store' }}
                        </div>
                        <div>{{ receiptData.number }}</div>
                        <div>
                            {{ receiptData.payload?.completed_at }}
                        </div>
                    </div>
                    <div class="my-2 border-t border-dashed" />
                    <div
                        v-for="item in receiptData.payload?.items"
                        :key="item.id"
                        class="flex justify-between"
                    >
                        <span>{{ item.name }} x{{ item.quantity }}</span>
                        <span>{{ format(item.total) }}</span>
                    </div>
                    <div class="my-2 border-t border-dashed" />
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>{{ format(receiptData.payload?.subtotal) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Discount</span>
                        <span
                            >-{{
                                format(receiptData.payload?.discount_total)
                            }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span>Tax</span>
                        <span>{{
                            format(receiptData.payload?.tax_total)
                        }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold">
                        <span>TOTAL</span>
                        <span>{{ format(receiptData.payload?.total) }}</span>
                    </div>
                    <div
                        v-for="p in receiptData.payload?.payments"
                        :key="p.id"
                        class="flex justify-between"
                    >
                        <span class="uppercase">{{ p.method }}</span>
                        <span>{{ format(p.amount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Change</span>
                        <span>{{
                            format(receiptData.payload?.change_total)
                        }}</span>
                    </div>
                    <div class="my-2 border-t border-dashed" />
                    <div class="text-center">Thank you!</div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="receiptOpen = false"
                        >Close</Button
                    >
                    <Button @click="printReceipt">Print</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Open shift dialog -->
        <Dialog v-model:open="shiftOpen">
            <DialogContent class="max-w-sm">
                <DialogHeader>
                    <DialogTitle>Open Shift</DialogTitle>
                </DialogHeader>
                <form class="space-y-3" @submit.prevent="openShiftSubmit">
                    <div>
                        <label class="text-sm font-medium">Opening cash</label>
                        <Input
                            v-model.number="shiftForm.opening_cash"
                            type="number"
                            min="0"
                            step="0.01"
                        />
                        <InputError :message="shiftForm.errors.opening_cash" />
                    </div>
                    <DialogFooter>
                        <Button type="submit" :disabled="shiftForm.processing"
                            >Open Shift</Button
                        >
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #receipt,
    #receipt * {
        visibility: visible;
    }
    #receipt {
        position: absolute;
        left: 0;
        top: 0;
        width: 80mm;
    }
}
</style>
