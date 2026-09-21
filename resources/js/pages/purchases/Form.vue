<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus, X } from '@lucide/vue';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useMoney } from '@/composables/useMoney';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, store } from '@/routes/purchase-orders';

const props = defineProps<{
    suppliers: { id: number; name: string }[];
    products: { id: number; name: string; sku: string | null; cost_price: string }[];
}>();

const { format } = useMoney();

const form = useForm<{
    supplier_id: string;
    expected_at: string;
    notes: string;
    status: string;
    items: { product_id: string; quantity: number; unit_cost: number }[];
}>({
    supplier_id: '',
    expected_at: '',
    notes: '',
    status: 'ordered',
    items: [{ product_id: '', quantity: 1, unit_cost: 0 }],
});

const total = computed(() =>
    form.items.reduce((s, i) => s + (Number(i.quantity) || 0) * (Number(i.unit_cost) || 0), 0),
);

function submit() {
    form.post(store.url());
}
</script>

<template>
    <Head title="New Purchase Order" />
    <AppLayout>
        <form class="space-y-4 p-4" @submit.prevent="submit">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">New Purchase Order</h1>
                <div class="flex gap-2">
                    <Button variant="outline" as-child><Link :href="index()">Cancel</Link></Button>
                    <Button type="submit" :disabled="form.processing">Save PO</Button>
                </div>
            </div>

            <Card>
                <CardContent class="grid grid-cols-3 gap-4 pt-6">
                    <div>
                        <Label>Supplier</Label>
                        <Select v-model="form.supplier_id">
                            <SelectTrigger><SelectValue placeholder="Select supplier" /></SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="s in suppliers" :key="s.id" :value="String(s.id)">{{ s.name }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.supplier_id" />
                    </div>
                    <div>
                        <Label>Expected date</Label>
                        <Input v-model="form.expected_at" type="date" />
                    </div>
                    <div>
                        <Label>Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="draft">Draft</SelectItem>
                                <SelectItem value="ordered">Ordered</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="col-span-3">
                        <Label>Notes</Label>
                        <Input v-model="form.notes" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Items</CardTitle>
                    <Button
                        type="button"
                        size="sm"
                        variant="outline"
                        @click="form.items.push({ product_id: '', quantity: 1, unit_cost: 0 })"
                    >
                        <Plus class="mr-1 h-4 w-4" />Add item
                    </Button>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div v-for="(item, i) in form.items" :key="i" class="flex items-center gap-2">
                        <Select v-model="item.product_id">
                            <SelectTrigger class="flex-1"><SelectValue placeholder="Product" /></SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="p in products" :key="p.id" :value="String(p.id)">
                                    {{ p.name }} ({{ p.sku }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Input v-model.number="item.quantity" type="number" min="0.001" step="0.001" placeholder="Qty" class="w-24" />
                        <Input v-model.number="item.unit_cost" type="number" min="0" step="0.01" placeholder="Unit cost" class="w-32" />
                        <div class="w-28 text-right text-sm font-medium">
                            {{ format(item.quantity * item.unit_cost) }}
                        </div>
                        <Button type="button" size="icon" variant="ghost" @click="form.items.splice(i, 1)">
                            <X class="h-4 w-4" />
                        </Button>
                    </div>
                    <InputError :message="form.errors.items" />
                    <div class="flex justify-end border-t pt-3 text-lg font-bold">
                        Total: {{ format(total) }}
                    </div>
                </CardContent>
            </Card>
        </form>
    </AppLayout>
</template>
