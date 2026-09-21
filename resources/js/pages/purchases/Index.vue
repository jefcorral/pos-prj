<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Plus } from '@lucide/vue';
import { ref, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useMoney } from '@/composables/useMoney';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, index, show } from '@/routes/purchase-orders';

const props = defineProps<{
    orders: {
        data: {
            id: number;
            reference_no: string;
            status: string;
            total: string;
            expected_at: string | null;
            created_at: string;
            items_count: number;
            supplier: { name: string } | null;
            branch: { name: string } | null;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { status?: string };
}>();

const { format } = useMoney();
const status = ref(props.filters.status ?? 'all');
watch(status, () => {
    router.get(
        index.url(),
        { status: status.value !== 'all' ? status.value : undefined },
        { preserveState: true, replace: true },
    );
});
</script>

<template>
    <Head title="Purchase Orders" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Purchase Orders</h1>
                <Button as-child>
                    <Link :href="create()"
                        ><Plus class="mr-1 h-4 w-4" />New PO</Link
                    >
                </Button>
            </div>

            <Select v-model="status">
                <SelectTrigger class="w-52"><SelectValue /></SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All statuses</SelectItem>
                    <SelectItem
                        v-for="s in [
                            'draft',
                            'ordered',
                            'partially_received',
                            'received',
                            'cancelled',
                        ]"
                        :key="s"
                        :value="s"
                    >
                        {{ s.replace('_', ' ') }}
                    </SelectItem>
                </SelectContent>
            </Select>

            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b text-left">
                        <tr>
                            <th class="p-3">Reference</th>
                            <th class="p-3">Supplier</th>
                            <th class="p-3">Branch</th>
                            <th class="p-3 text-right">Items</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3">Expected</th>
                            <th class="p-3">Status</th>
                            <th class="p-3" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="o in orders.data"
                            :key="o.id"
                            class="border-b last:border-0"
                        >
                            <td class="p-3 font-mono font-medium">
                                {{ o.reference_no }}
                            </td>
                            <td class="p-3">{{ o.supplier?.name }}</td>
                            <td class="p-3">{{ o.branch?.name }}</td>
                            <td class="p-3 text-right">{{ o.items_count }}</td>
                            <td class="p-3 text-right font-medium">
                                {{ format(o.total) }}
                            </td>
                            <td class="text-muted-foreground p-3">
                                {{ o.expected_at ?? '—' }}
                            </td>
                            <td class="p-3">
                                <Badge>{{ o.status.replace('_', ' ') }}</Badge>
                            </td>
                            <td class="p-3 text-right">
                                <Button size="icon" variant="ghost" as-child>
                                    <Link :href="show.url(o.id)"
                                        ><Eye class="h-4 w-4"
                                    /></Link>
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="!orders.data.length">
                            <td
                                colspan="8"
                                class="text-muted-foreground p-8 text-center"
                            >
                                No purchase orders.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
