<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { AlertTriangle, ArrowRight } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useMoney } from '@/composables/useMoney';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as posIndex } from '@/routes/pos';
import { show as saleShow } from '@/routes/sales';

defineProps<{
    stats: {
        today_sales: number;
        today_transactions: number;
        today_items: number;
        average_transaction: number;
        low_stock_count: number;
    };
    trend: { date: string; total: string; count: number }[];
    recentSales: {
        id: number;
        number: string;
        total: string;
        status: string;
        completed_at: string | null;
        cashier: { name: string } | null;
    }[];
    topProducts: {
        product_id: number;
        name: string;
        qty: string;
        revenue: string;
    }[];
}>();

const { format } = useMoney();
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Dashboard</h1>
                <Link
                    :href="posIndex()"
                    class="text-primary inline-flex items-center gap-1 text-sm font-medium hover:underline"
                >
                    Open POS <ArrowRight class="h-4 w-4" />
                </Link>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <Card>
                    <CardContent class="p-4">
                        <div class="text-muted-foreground text-xs">
                            Today's sales
                        </div>
                        <div class="text-2xl font-bold">
                            {{ format(stats.today_sales) }}
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4">
                        <div class="text-muted-foreground text-xs">
                            Transactions
                        </div>
                        <div class="text-2xl font-bold">
                            {{ stats.today_transactions }}
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4">
                        <div class="text-muted-foreground text-xs">
                            Items sold
                        </div>
                        <div class="text-2xl font-bold">
                            {{ stats.today_items }}
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4">
                        <div class="text-muted-foreground text-xs">
                            Avg. transaction
                        </div>
                        <div class="text-2xl font-bold">
                            {{ format(stats.average_transaction) }}
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4">
                        <div
                            class="text-muted-foreground flex items-center gap-1 text-xs"
                        >
                            <AlertTriangle class="h-3 w-3" /> Low stock
                        </div>
                        <div
                            class="text-2xl font-bold"
                            :class="
                                stats.low_stock_count > 0
                                    ? 'text-destructive'
                                    : ''
                            "
                        >
                            {{ stats.low_stock_count }}
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <Card class="lg:col-span-2">
                    <CardHeader
                        ><CardTitle
                            >Sales trend (14 days)</CardTitle
                        ></CardHeader
                    >
                    <CardContent>
                        <div class="flex h-40 items-end gap-1">
                            <div
                                v-for="d in trend"
                                :key="d.date"
                                class="bg-primary/80 flex-1 rounded-t"
                                :style="{
                                    height: `${Math.max(4, (Number(d.total) / Math.max(...trend.map((t) => Number(t.total)), 1)) * 100)}%`,
                                }"
                                :title="`${d.date}: ${format(d.total)}`"
                            />
                            <p
                                v-if="!trend.length"
                                class="text-muted-foreground w-full self-center text-center text-sm"
                            >
                                No sales yet.
                            </p>
                        </div>
                        <div
                            class="text-muted-foreground mt-1 flex justify-between text-xs"
                        >
                            <span>{{ trend[0]?.date }}</span>
                            <span>{{ trend[trend.length - 1]?.date }}</span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        ><CardTitle>Top products (30d)</CardTitle></CardHeader
                    >
                    <CardContent class="space-y-2 text-sm">
                        <div
                            v-for="p in topProducts"
                            :key="p.product_id"
                            class="flex justify-between"
                        >
                            <span class="truncate">{{ p.name }}</span>
                            <span class="font-medium">{{
                                format(p.revenue)
                            }}</span>
                        </div>
                        <p
                            v-if="!topProducts.length"
                            class="text-muted-foreground"
                        >
                            No data yet.
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader><CardTitle>Recent sales</CardTitle></CardHeader>
                <CardContent>
                    <table class="w-full text-sm">
                        <tbody>
                            <tr
                                v-for="s in recentSales"
                                :key="s.id"
                                class="border-b last:border-0"
                            >
                                <td class="py-2">
                                    <Link
                                        :href="saleShow.url(s.id)"
                                        class="font-mono font-medium hover:underline"
                                    >
                                        {{ s.number }}
                                    </Link>
                                </td>
                                <td class="py-2">
                                    {{ s.cashier?.name ?? '—' }}
                                </td>
                                <td class="py-2">
                                    <Badge variant="outline">{{
                                        s.status.replace('_', ' ')
                                    }}</Badge>
                                </td>
                                <td class="text-muted-foreground py-2">
                                    {{
                                        s.completed_at
                                            ? new Date(
                                                  s.completed_at,
                                              ).toLocaleString()
                                            : '—'
                                    }}
                                </td>
                                <td class="py-2 text-right font-medium">
                                    {{ format(s.total) }}
                                </td>
                            </tr>
                            <tr v-if="!recentSales.length">
                                <td
                                    class="text-muted-foreground py-4 text-center"
                                >
                                    No sales yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
