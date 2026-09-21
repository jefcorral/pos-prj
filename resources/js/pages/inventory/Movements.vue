<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { index, movements } from '@/routes/inventory';

const props = defineProps<{
    movements: {
        data: {
            id: number;
            type: string;
            quantity: string;
            quantity_after: string;
            note: string | null;
            created_at: string;
            product: { name: string; sku: string | null } | null;
            variant: { name: string } | null;
            user: { name: string } | null;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { type?: string; from?: string; to?: string };
}>();

const type = ref(props.filters.type ?? 'all');
const from = ref(props.filters.from ?? '');
const to = ref(props.filters.to ?? '');

watch([type, from, to], () => {
    router.get(
        movements.url(),
        {
            type: type.value !== 'all' ? type.value : undefined,
            from: from.value || undefined,
            to: to.value || undefined,
        },
        { preserveState: true, replace: true },
    );
});
</script>

<template>
    <Head title="Inventory Movements" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center gap-3">
                <Button variant="ghost" size="icon" as-child>
                    <Link :href="index()"><ArrowLeft class="h-4 w-4" /></Link>
                </Button>
                <h1 class="text-xl font-bold">Movement History</h1>
            </div>

            <div class="flex gap-3">
                <Select v-model="type">
                    <SelectTrigger class="w-44"><SelectValue placeholder="All types" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All types</SelectItem>
                        <SelectItem v-for="t in ['purchase', 'sale', 'return', 'adjustment', 'transfer']" :key="t" :value="t">
                            {{ t }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <Input v-model="from" type="date" class="w-40" />
                <Input v-model="to" type="date" class="w-40" />
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50 text-left">
                        <tr>
                            <th class="p-3">Date</th>
                            <th class="p-3">Product</th>
                            <th class="p-3">Type</th>
                            <th class="p-3 text-right">Qty</th>
                            <th class="p-3 text-right">After</th>
                            <th class="p-3">Note</th>
                            <th class="p-3">By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="m in movements.data" :key="m.id" class="border-b last:border-0">
                            <td class="p-3 text-muted-foreground">
                                {{ new Date(m.created_at).toLocaleString() }}
                            </td>
                            <td class="p-3">
                                {{ m.product?.name }}
                                <span v-if="m.variant" class="text-muted-foreground">({{ m.variant.name }})</span>
                            </td>
                            <td class="p-3"><Badge variant="outline" class="uppercase">{{ m.type }}</Badge></td>
                            <td
                                class="p-3 text-right font-medium"
                                :class="Number(m.quantity) < 0 ? 'text-destructive' : 'text-green-600'"
                            >
                                {{ m.quantity }}
                            </td>
                            <td class="p-3 text-right">{{ m.quantity_after }}</td>
                            <td class="p-3 text-muted-foreground">{{ m.note ?? '—' }}</td>
                            <td class="p-3">{{ m.user?.name ?? '—' }}</td>
                        </tr>
                        <tr v-if="!movements.data.length">
                            <td colspan="7" class="p-8 text-center text-muted-foreground">No movements.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex gap-1">
                <Button
                    v-for="l in movements.links"
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
