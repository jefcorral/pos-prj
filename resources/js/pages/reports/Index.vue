<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
import { index } from '@/routes/reports';

const props = defineProps<{
    filters: { from: string; to: string; branch_id: number | null };
    branches: { id: number; name: string }[];
    summary: {
        gross_sales: number;
        discounts: number;
        taxes: number;
        refunds: number;
        net_sales: number;
        transactions: number;
    };
    dailySales: { date: string; total: string; count: number }[];
    byPaymentMethod: { method: string; total: string; count: number }[];
    byProduct: { name: string; qty: string; revenue: string }[];
    byCategory: { category: string; qty: string; revenue: string }[];
    byCashier: { cashier: string; total: string; count: number }[];
    inventory: {
        cost_value: number;
        retail_value: number;
        low_stock: {
            quantity: string;
            product: { name: string } | null;
            branch: { name: string } | null;
        }[];
        movements: { type: string; qty: string; count: number }[];
    };
}>();

const { format } = useMoney();
const from = ref(props.filters.from);
const to = ref(props.filters.to);
const branchId = ref(
    props.filters.branch_id ? String(props.filters.branch_id) : 'all',
);

watch([from, to, branchId], () => {
    router.get(
        index.url(),
        {
            from: from.value,
            to: to.value,
            branch_id: branchId.value !== 'all' ? branchId.value : undefined,
        },
        { preserveState: true, replace: true },
    );
});
</script>

<template>
    <Head title="Reports" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-xl font-bold">Reports</h1>
                <Input v-model="from" type="date" class="w-40" />
                <Input v-model="to" type="date" class="w-40" />
                <Select v-model="branchId">
                    <SelectTrigger class="w-44"><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All branches</SelectItem>
                        <SelectItem
                            v-for="b in branches"
                            :key="b.id"
                            :value="String(b.id)"
                            >{{ b.name }}</SelectItem
                        >
                    </SelectContent>
                </Select>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
                <Card
                    v-for="[label, value] in [
                        ['Gross sales', format(summary.gross_sales)],
                        ['Discounts', format(summary.discounts)],
                        ['Taxes', format(summary.taxes)],
                        ['Refunds', format(summary.refunds)],
                        ['Net sales', format(summary.net_sales)],
                        ['Transactions', String(summary.transactions)],
                    ]"
                    :key="label"
                >
                    <CardContent class="p-4">
                        <div class="text-muted-foreground text-xs">
                            {{ label }}
                        </div>
                        <div class="text-lg font-bold">{{ value }}</div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <Card>
                    <CardHeader><CardTitle>Daily sales</CardTitle></CardHeader>
                    <CardContent>
                        <table class="w-full text-sm">
                            <tbody>
                                <tr
                                    v-for="d in dailySales"
                                    :key="d.date"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-1.5">{{ d.date }}</td>
                                    <td
                                        class="text-muted-foreground py-1.5 text-right"
                                    >
                                        {{ d.count }} txns
                                    </td>
                                    <td class="py-1.5 text-right font-medium">
                                        {{ format(d.total) }}
                                    </td>
                                </tr>
                                <tr v-if="!dailySales.length">
                                    <td
                                        class="text-muted-foreground py-4 text-center"
                                    >
                                        No sales in range.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        ><CardTitle>By payment method</CardTitle></CardHeader
                    >
                    <CardContent>
                        <table class="w-full text-sm">
                            <tbody>
                                <tr
                                    v-for="m in byPaymentMethod"
                                    :key="m.method"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-1.5 uppercase">
                                        {{ m.method.replace('_', ' ') }}
                                    </td>
                                    <td
                                        class="text-muted-foreground py-1.5 text-right"
                                    >
                                        {{ m.count }}
                                    </td>
                                    <td class="py-1.5 text-right font-medium">
                                        {{ format(m.total) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>Top products</CardTitle></CardHeader>
                    <CardContent>
                        <table class="w-full text-sm">
                            <tbody>
                                <tr
                                    v-for="p in byProduct"
                                    :key="p.name"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-1.5">{{ p.name }}</td>
                                    <td
                                        class="text-muted-foreground py-1.5 text-right"
                                    >
                                        {{ p.qty }}
                                    </td>
                                    <td class="py-1.5 text-right font-medium">
                                        {{ format(p.revenue) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>By category</CardTitle></CardHeader>
                    <CardContent>
                        <table class="w-full text-sm">
                            <tbody>
                                <tr
                                    v-for="c in byCategory"
                                    :key="c.category"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-1.5">{{ c.category }}</td>
                                    <td
                                        class="text-muted-foreground py-1.5 text-right"
                                    >
                                        {{ c.qty }}
                                    </td>
                                    <td class="py-1.5 text-right font-medium">
                                        {{ format(c.revenue) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>By cashier</CardTitle></CardHeader>
                    <CardContent>
                        <table class="w-full text-sm">
                            <tbody>
                                <tr
                                    v-for="c in byCashier"
                                    :key="c.cashier"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-1.5">{{ c.cashier }}</td>
                                    <td
                                        class="text-muted-foreground py-1.5 text-right"
                                    >
                                        {{ c.count }}
                                    </td>
                                    <td class="py-1.5 text-right font-medium">
                                        {{ format(c.total) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>Inventory</CardTitle></CardHeader>
                    <CardContent class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span>Stock value (cost)</span
                            ><span class="font-medium">{{
                                format(inventory.cost_value)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Stock value (retail)</span
                            ><span class="font-medium">{{
                                format(inventory.retail_value)
                            }}</span>
                        </div>
                        <div>
                            <div class="mb-1 font-semibold">Low stock</div>
                            <div
                                v-for="(l, i) in inventory.low_stock"
                                :key="i"
                                class="text-muted-foreground flex justify-between"
                            >
                                <span
                                    >{{ l.product?.name }} ({{
                                        l.branch?.name
                                    }})</span
                                >
                                <span class="text-destructive">{{
                                    l.quantity
                                }}</span>
                            </div>
                            <div
                                v-if="!inventory.low_stock.length"
                                class="text-muted-foreground"
                            >
                                None.
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
