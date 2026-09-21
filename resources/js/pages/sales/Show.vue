<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Printer, Undo2, XCircle } from '@lucide/vue';
import { ref } from 'vue';
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
import { Label } from '@/components/ui/label';
import { useMoney } from '@/composables/useMoney';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, refund as refundRoute, voidMethod } from '@/routes/sales';

const props = defineProps<{
    sale: {
        id: number;
        number: string;
        status: string;
        subtotal: string;
        discount_total: string;
        tax_total: string;
        total: string;
        paid_total: string;
        change_total: string;
        completed_at: string | null;
        void_reason: string | null;
        cashier: { name: string } | null;
        customer: { name: string; phone: string | null } | null;
        branch: { name: string; code: string } | null;
        items: {
            id: number;
            name: string;
            quantity: string;
            quantity_refunded: string;
            unit_price: string;
            discount: string;
            tax: string;
            total: string;
        }[];
        payments: { id: number; method: string; amount: string; tendered: string | null; change: string; reference: string | null }[];
        refunds: { id: number; amount: string; reason: string | null; created_at: string }[];
    };
    can: { void: boolean; refund: boolean };
}>();

const { format } = useMoney();
const voidOpen = ref(false);
const refundOpen = ref(false);

const voidForm = useForm({ reason: '' });
const refundForm = useForm<{ reason: string; items: { sale_item_id: number; quantity: number }[] }>({
    reason: '',
    items: [],
});

const refundQty = ref<Record<number, number>>({});

function submitVoid() {
    voidForm.post(voidMethod.url(props.sale.id), {
        onSuccess: () => (voidOpen.value = false),
    });
}

function submitRefund() {
    refundForm.items = Object.entries(refundQty.value)
        .filter(([, q]) => q > 0)
        .map(([id, quantity]) => ({ sale_item_id: Number(id), quantity }));
    refundForm.post(refundRoute.url(props.sale.id), {
        onSuccess: () => {
            refundOpen.value = false;
            refundQty.value = {};
        },
    });
}

function printReceipt() {
    window.open(`/sales/${props.sale.id}/receipt`, '_blank');
}
</script>

<template>
    <Head :title="`Sale ${sale.number}`" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Button variant="ghost" size="icon" as-child>
                        <Link :href="index()"><ArrowLeft class="h-4 w-4" /></Link>
                    </Button>
                    <h1 class="font-mono text-xl font-bold">{{ sale.number }}</h1>
                    <Badge>{{ sale.status.replace('_', ' ') }}</Badge>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="printReceipt">
                        <Printer class="mr-1 h-4 w-4" />Receipt
                    </Button>
                    <Button
                        v-if="can.refund && ['completed', 'partially_refunded'].includes(sale.status)"
                        variant="outline"
                        @click="refundOpen = true"
                        ><Undo2 class="mr-1 h-4 w-4" />Refund</Button
                    >
                    <Button
                        v-if="can.void && !['voided', 'refunded'].includes(sale.status)"
                        variant="destructive"
                        @click="voidOpen = true"
                        ><XCircle class="mr-1 h-4 w-4" />Void</Button
                    >
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-lg border lg:col-span-2">
                    <div class="border-b p-3 font-semibold">Items</div>
                    <table class="w-full text-sm">
                        <thead class="border-b bg-muted/50 text-left">
                            <tr>
                                <th class="p-3">Item</th>
                                <th class="p-3 text-right">Qty</th>
                                <th class="p-3 text-right">Price</th>
                                <th class="p-3 text-right">Disc.</th>
                                <th class="p-3 text-right">Tax</th>
                                <th class="p-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="i in sale.items" :key="i.id" class="border-b last:border-0">
                                <td class="p-3">{{ i.name }}</td>
                                <td class="p-3 text-right">
                                    {{ i.quantity }}
                                    <span v-if="Number(i.quantity_refunded) > 0" class="text-xs text-destructive">
                                        (-{{ i.quantity_refunded }} refunded)
                                    </span>
                                </td>
                                <td class="p-3 text-right">{{ format(i.unit_price) }}</td>
                                <td class="p-3 text-right">{{ format(i.discount) }}</td>
                                <td class="p-3 text-right">{{ format(i.tax) }}</td>
                                <td class="p-3 text-right font-medium">{{ format(i.total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1 rounded-lg border p-4 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span>{{ format(sale.subtotal) }}</span></div>
                        <div class="flex justify-between"><span>Discount</span><span>-{{ format(sale.discount_total) }}</span></div>
                        <div class="flex justify-between"><span>Tax</span><span>{{ format(sale.tax_total) }}</span></div>
                        <div class="flex justify-between border-t pt-2 text-lg font-bold">
                            <span>Total</span><span>{{ format(sale.total) }}</span>
                        </div>
                        <div class="flex justify-between text-muted-foreground">
                            <span>Paid</span><span>{{ format(sale.paid_total) }}</span>
                        </div>
                        <div class="flex justify-between text-muted-foreground">
                            <span>Change</span><span>{{ format(sale.change_total) }}</span>
                        </div>
                    </div>

                    <div class="rounded-lg border p-4 text-sm">
                        <div class="mb-2 font-semibold">Payments</div>
                        <div v-for="p in sale.payments" :key="p.id" class="flex justify-between">
                            <span class="uppercase">{{ p.method }}<span v-if="p.reference" class="text-muted-foreground"> · {{ p.reference }}</span></span>
                            <span>{{ format(p.amount) }}</span>
                        </div>
                    </div>

                    <div v-if="sale.refunds.length" class="rounded-lg border p-4 text-sm">
                        <div class="mb-2 font-semibold">Refunds</div>
                        <div v-for="r in sale.refunds" :key="r.id" class="flex justify-between">
                            <span>{{ new Date(r.created_at).toLocaleDateString() }}</span>
                            <span class="text-destructive">-{{ format(r.amount) }}</span>
                        </div>
                    </div>

                    <div class="rounded-lg border p-4 text-sm text-muted-foreground">
                        <div>Cashier: {{ sale.cashier?.name ?? '—' }}</div>
                        <div>Branch: {{ sale.branch?.name ?? '—' }}</div>
                        <div>Customer: {{ sale.customer?.name ?? 'Walk-in' }}</div>
                        <div>Completed: {{ sale.completed_at ? new Date(sale.completed_at).toLocaleString() : '—' }}</div>
                        <div v-if="sale.void_reason" class="text-destructive">Void reason: {{ sale.void_reason }}</div>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="voidOpen">
            <DialogContent class="max-w-md">
                <DialogHeader><DialogTitle>Void {{ sale.number }}</DialogTitle></DialogHeader>
                <form class="space-y-3" @submit.prevent="submitVoid">
                    <p class="text-sm text-muted-foreground">
                        This will restock all items and reverse cash movements. This cannot be undone.
                    </p>
                    <div>
                        <Label>Reason</Label>
                        <Input v-model="voidForm.reason" />
                        <InputError :message="voidForm.errors.reason" />
                    </div>
                    <DialogFooter>
                        <Button type="submit" variant="destructive" :disabled="voidForm.processing">Void Sale</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="refundOpen">
            <DialogContent class="max-w-md">
                <DialogHeader><DialogTitle>Refund {{ sale.number }}</DialogTitle></DialogHeader>
                <form class="space-y-3" @submit.prevent="submitRefund">
                    <div
                        v-for="i in sale.items"
                        :key="i.id"
                        class="flex items-center justify-between gap-3 text-sm"
                    >
                        <span class="flex-1">{{ i.name }}</span>
                        <span class="text-xs text-muted-foreground">
                            refundable: {{ Number(i.quantity) - Number(i.quantity_refunded) }}
                        </span>
                        <Input
                            v-model.number="refundQty[i.id]"
                            type="number"
                            min="0"
                            :max="Number(i.quantity) - Number(i.quantity_refunded)"
                            step="0.001"
                            class="w-24"
                        />
                    </div>
                    <div>
                        <Label>Reason</Label>
                        <Input v-model="refundForm.reason" />
                    </div>
                    <InputError :message="refundForm.errors.items" />
                    <DialogFooter>
                        <Button type="submit" :disabled="refundForm.processing">Process Refund</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
