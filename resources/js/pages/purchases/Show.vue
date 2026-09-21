<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, PackageCheck } from '@lucide/vue';
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
import { index, receive, update } from '@/routes/purchase-orders';

const props = defineProps<{
    order: {
        id: number;
        reference_no: string;
        status: string;
        total: string;
        expected_at: string | null;
        notes: string | null;
        supplier: { name: string; contact_name: string | null } | null;
        items: {
            id: number;
            quantity_ordered: string;
            quantity_received: string;
            unit_cost: string;
            total: string;
            product: { name: string; sku: string | null } | null;
            variant: { name: string } | null;
        }[];
        receipts: { id: number; received_at: string; items: { quantity: string }[] }[];
    };
}>();

const { format } = useMoney();
const receiveOpen = ref(false);
const receiveQty = ref<Record<number, number>>({});
const receiveForm = useForm<{ items: { purchase_order_item_id: number; quantity: number }[]; note: string }>({
    items: [],
    note: '',
});

function submitReceive() {
    receiveForm.items = Object.entries(receiveQty.value)
        .filter(([, q]) => q > 0)
        .map(([id, quantity]) => ({ purchase_order_item_id: Number(id), quantity }));
    receiveForm.post(receive.url(props.order.id), {
        onSuccess: () => {
            receiveOpen.value = false;
            receiveQty.value = {};
        },
    });
}

const outstanding = (item: { quantity_ordered: string; quantity_received: string }) =>
    Number(item.quantity_ordered) - Number(item.quantity_received);
</script>

<template>
    <Head :title="`PO ${order.reference_no}`" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Button variant="ghost" size="icon" as-child>
                        <Link :href="index()"><ArrowLeft class="h-4 w-4" /></Link>
                    </Button>
                    <h1 class="font-mono text-xl font-bold">{{ order.reference_no }}</h1>
                    <Badge>{{ order.status.replace('_', ' ') }}</Badge>
                </div>
                <div class="flex gap-2">
                    <Button
                        v-if="order.status === 'draft'"
                        variant="outline"
                        @click="useForm({ status: 'ordered' }).put(update.url(order.id))"
                    >Mark Ordered</Button>
                    <Button
                        v-if="['ordered', 'partially_received'].includes(order.status)"
                        @click="receiveOpen = true"
                    >
                        <PackageCheck class="mr-1 h-4 w-4" />Receive Items
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-lg border lg:col-span-2">
                    <div class="border-b p-3 font-semibold">Items</div>
                    <table class="w-full text-sm">
                        <thead class="border-b bg-muted/50 text-left">
                            <tr>
                                <th class="p-3">Product</th>
                                <th class="p-3 text-right">Ordered</th>
                                <th class="p-3 text-right">Received</th>
                                <th class="p-3 text-right">Unit cost</th>
                                <th class="p-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="i in order.items" :key="i.id" class="border-b last:border-0">
                                <td class="p-3">
                                    {{ i.product?.name }}
                                    <span v-if="i.variant" class="text-muted-foreground">({{ i.variant.name }})</span>
                                </td>
                                <td class="p-3 text-right">{{ i.quantity_ordered }}</td>
                                <td class="p-3 text-right">{{ i.quantity_received }}</td>
                                <td class="p-3 text-right">{{ format(i.unit_cost) }}</td>
                                <td class="p-3 text-right font-medium">{{ format(i.total) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="border-t font-bold">
                                <td colspan="4" class="p-3 text-right">Total</td>
                                <td class="p-3 text-right">{{ format(order.total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="rounded-lg border p-4 text-sm">
                    <div class="mb-2 font-semibold">Details</div>
                    <div>Supplier: {{ order.supplier?.name }}</div>
                    <div>Expected: {{ order.expected_at ?? '—' }}</div>
                    <div v-if="order.notes" class="mt-2 text-muted-foreground">{{ order.notes }}</div>
                    <div class="mt-3 font-semibold">Receipts</div>
                    <div v-for="r in order.receipts" :key="r.id" class="text-muted-foreground">
                        {{ new Date(r.received_at).toLocaleString() }} — {{ r.items.length }} lines
                    </div>
                    <div v-if="!order.receipts.length" class="text-muted-foreground">None yet.</div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="receiveOpen">
            <DialogContent class="max-w-md">
                <DialogHeader><DialogTitle>Receive Items — {{ order.reference_no }}</DialogTitle></DialogHeader>
                <form class="space-y-3" @submit.prevent="submitReceive">
                    <div v-for="i in order.items" :key="i.id" class="flex items-center justify-between gap-3 text-sm">
                        <span class="flex-1">{{ i.product?.name }}</span>
                        <span class="text-xs text-muted-foreground">outstanding: {{ outstanding(i) }}</span>
                        <Input
                            v-model.number="receiveQty[i.id]"
                            type="number"
                            min="0"
                            :max="outstanding(i)"
                            step="0.001"
                            class="w-24"
                        />
                    </div>
                    <div>
                        <Label>Note</Label>
                        <Input v-model="receiveForm.note" />
                    </div>
                    <InputError :message="receiveForm.errors.items" />
                    <DialogFooter>
                        <Button type="submit" :disabled="receiveForm.processing">Receive & Stock In</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
