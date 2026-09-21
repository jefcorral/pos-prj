<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Search } from '@lucide/vue';
import { ref, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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
import { index, show } from '@/routes/sales';

const props = defineProps<{
    sales: {
        data: {
            id: number;
            number: string;
            status: string;
            total: string;
            completed_at: string | null;
            created_at: string;
            cashier: { name: string } | null;
            customer: { name: string } | null;
            payments: { method: string; amount: string }[];
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { status?: string; search?: string; from?: string; to?: string };
}>();

const { format } = useMoney();
const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? 'all');
const from = ref(props.filters.from ?? '');
const to = ref(props.filters.to ?? '');

watch([search, status, from, to], () => {
    router.get(
        index.url(),
        {
            search: search.value || undefined,
            status: status.value !== 'all' ? status.value : undefined,
            from: from.value || undefined,
            to: to.value || undefined,
        },
        { preserveState: true, replace: true },
    );
});

const statusVariant = (s: string) =>
    s === 'completed'
        ? 'secondary'
        : s === 'held'
          ? 'outline'
          : s === 'voided' || s === 'refunded'
            ? 'destructive'
            : 'default';
</script>

<template>
    <Head title="Sales" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <h1 class="text-xl font-bold">Sales</h1>

            <div class="flex flex-wrap gap-3">
                <div class="relative w-64">
                    <Search
                        class="text-muted-foreground absolute top-2.5 left-3 h-4 w-4"
                    />
                    <Input
                        v-model="search"
                        placeholder="Sale #…"
                        class="pl-9"
                    />
                </div>
                <Select v-model="status">
                    <SelectTrigger class="w-44"><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All statuses</SelectItem>
                        <SelectItem
                            v-for="s in [
                                'completed',
                                'held',
                                'voided',
                                'refunded',
                                'partially_refunded',
                            ]"
                            :key="s"
                            :value="s"
                            >{{ s.replace('_', ' ') }}</SelectItem
                        >
                    </SelectContent>
                </Select>
                <Input v-model="from" type="date" class="w-40" />
                <Input v-model="to" type="date" class="w-40" />
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b text-left">
                        <tr>
                            <th class="p-3">Sale #</th>
                            <th class="p-3">Date</th>
                            <th class="p-3">Cashier</th>
                            <th class="p-3">Customer</th>
                            <th class="p-3">Payments</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3">Status</th>
                            <th class="p-3" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="s in sales.data"
                            :key="s.id"
                            class="border-b last:border-0"
                        >
                            <td class="p-3 font-mono font-medium">
                                {{ s.number }}
                            </td>
                            <td class="text-muted-foreground p-3">
                                {{
                                    new Date(
                                        s.completed_at ?? s.created_at,
                                    ).toLocaleString()
                                }}
                            </td>
                            <td class="p-3">{{ s.cashier?.name ?? '—' }}</td>
                            <td class="p-3">
                                {{ s.customer?.name ?? 'Walk-in' }}
                            </td>
                            <td class="p-3">
                                <Badge
                                    v-for="(p, i) in s.payments"
                                    :key="i"
                                    variant="outline"
                                    class="mr-1 uppercase"
                                >
                                    {{ p.method }}
                                </Badge>
                            </td>
                            <td class="p-3 text-right font-medium">
                                {{ format(s.total) }}
                            </td>
                            <td class="p-3">
                                <Badge :variant="statusVariant(s.status)">{{
                                    s.status.replace('_', ' ')
                                }}</Badge>
                            </td>
                            <td class="p-3 text-right">
                                <Button size="icon" variant="ghost" as-child>
                                    <Link :href="show.url(s.id)"
                                        ><Eye class="h-4 w-4"
                                    /></Link>
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="!sales.data.length">
                            <td
                                colspan="8"
                                class="text-muted-foreground p-8 text-center"
                            >
                                No sales.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex gap-1">
                <Button
                    v-for="l in sales.links"
                    :key="l.label"
                    size="sm"
                    :variant="l.active ? 'default' : 'outline'"
                    :disabled="!l.url"
                    @click="l.url && router.get(l.url)"
                    v-html="l.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
