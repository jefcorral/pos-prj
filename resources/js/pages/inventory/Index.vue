<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Plus, Search } from '@lucide/vue';
import { ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { usePermissions } from '@/composables/usePermissions';
import AppLayout from '@/layouts/AppLayout.vue';
import { adjust, index, movements } from '@/routes/inventory';

const props = defineProps<{
    inventory: {
        data: {
            id: number;
            quantity: string;
            product: {
                id: number;
                name: string;
                sku: string | null;
                low_stock_threshold: number;
                category: { name: string } | null;
            } | null;
            variant: { id: number; name: string } | null;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    lowStockCount: number;
    filters: { search?: string; low_stock?: boolean };
}>();

const { can } = usePermissions();
const search = ref(props.filters.search ?? '');
const lowStock = ref(!!props.filters.low_stock);
const adjustOpen = ref(false);

const adjustForm = useForm({
    product_id: '',
    quantity: '',
    reason: '',
    note: '',
    allow_negative: false,
});

watch([search, lowStock], () => {
    router.get(
        index.url(),
        {
            search: search.value || undefined,
            low_stock: lowStock.value || undefined,
        },
        { preserveState: true, replace: true },
    );
});

function submitAdjust() {
    adjustForm.post(adjust.url(), {
        onSuccess: () => {
            adjustOpen.value = false;
            adjustForm.reset();
        },
    });
}
</script>

<template>
    <Head title="Inventory" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">
                    Inventory
                    <Badge v-if="lowStockCount" variant="destructive" class="ml-2">
                        {{ lowStockCount }} low stock
                    </Badge>
                </h1>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="movements()">Movement History</Link>
                    </Button>
                    <Button v-if="can('inventory.adjust')" @click="adjustOpen = true">
                        <Plus class="mr-1 h-4 w-4" />Adjust Stock
                    </Button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative w-72">
                    <Search class="absolute top-2.5 left-3 h-4 w-4 text-muted-foreground" />
                    <Input v-model="search" placeholder="Search product…" class="pl-9" />
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <Checkbox v-model:checked="lowStock" /> Low stock only
                </label>
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50 text-left">
                        <tr>
                            <th class="p-3">Product</th>
                            <th class="p-3">SKU</th>
                            <th class="p-3">Category</th>
                            <th class="p-3 text-right">On hand</th>
                            <th class="p-3 text-right">Low-stock threshold</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="i in inventory.data" :key="i.id" class="border-b last:border-0">
                            <td class="p-3 font-medium">
                                {{ i.product?.name }}
                                <span v-if="i.variant" class="text-muted-foreground">({{ i.variant.name }})</span>
                            </td>
                            <td class="p-3 text-muted-foreground">{{ i.product?.sku }}</td>
                            <td class="p-3">{{ i.product?.category?.name ?? '—' }}</td>
                            <td class="p-3 text-right font-medium">{{ i.quantity }}</td>
                            <td class="p-3 text-right">{{ i.product?.low_stock_threshold }}</td>
                            <td class="p-3">
                                <Badge
                                    :variant="Number(i.quantity) <= (i.product?.low_stock_threshold ?? 0) ? 'destructive' : 'secondary'"
                                >
                                    {{ Number(i.quantity) <= (i.product?.low_stock_threshold ?? 0) ? 'Low' : 'OK' }}
                                </Badge>
                            </td>
                        </tr>
                        <tr v-if="!inventory.data.length">
                            <td colspan="6" class="p-8 text-center text-muted-foreground">No inventory records.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex gap-1">
                <Button
                    v-for="l in inventory.links"
                    :key="l.label"
                    size="sm"
                    :variant="l.active ? 'default' : 'outline'"
                    :disabled="!l.url"
                    @click="l.url && router.get(l.url)"
                    v-html="l.label"
                />
            </div>
        </div>

        <Dialog v-model:open="adjustOpen">
            <DialogContent class="max-w-md">
                <DialogHeader><DialogTitle>Adjust Stock</DialogTitle></DialogHeader>
                <form class="space-y-3" @submit.prevent="submitAdjust">
                    <div>
                        <Label>Product ID</Label>
                        <Input v-model="adjustForm.product_id" placeholder="Product ID (see Products list)" />
                        <InputError :message="adjustForm.errors.product_id" />
                    </div>
                    <div>
                        <Label>Quantity (+/-)</Label>
                        <Input v-model="adjustForm.quantity" type="number" step="0.001" placeholder="e.g. 10 or -3" />
                        <InputError :message="adjustForm.errors.quantity" />
                    </div>
                    <div>
                        <Label>Reason</Label>
                        <Input v-model="adjustForm.reason" placeholder="e.g. Damaged, Cycle count" />
                        <InputError :message="adjustForm.errors.reason" />
                    </div>
                    <div>
                        <Label>Note</Label>
                        <Input v-model="adjustForm.note" />
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <Checkbox v-model:checked="adjustForm.allow_negative" />
                        Allow negative stock
                    </label>
                    <InputError :message="adjustForm.errors.items" />
                    <DialogFooter>
                        <Button type="submit" :disabled="adjustForm.processing">Apply</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
