<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2 } from '@lucide/vue';
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
import { usePermissions } from '@/composables/usePermissions';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, index, edit } from '@/routes/products';

const props = defineProps<{
    products: {
        data: {
            id: number;
            name: string;
            sku: string | null;
            barcode: string | null;
            selling_price: string;
            is_active: boolean;
            low_stock_threshold: number;
            inventories_sum_quantity: string | null;
            category: { name: string } | null;
            brand: { name: string } | null;
            variants: { id: number; name: string }[];
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    categories: { id: number; name: string }[];
    filters: { search?: string; category_id?: string };
}>();

const { format } = useMoney();
const { can } = usePermissions();
const search = ref(props.filters.search ?? '');
const categoryId = ref(props.filters.category_id ?? 'all');

watch([search, categoryId], () => {
    router.get(
        index.url(),
        {
            search: search.value || undefined,
            category_id: categoryId.value !== 'all' ? categoryId.value : undefined,
        },
        { preserveState: true, replace: true },
    );
});

function remove(id: number) {
    if (confirm('Delete this product?')) {
        router.delete(destroy.url(id));
    }
}
</script>

<template>
    <Head title="Products" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Products</h1>
                <Button v-if="can('products.create')" as-child>
                    <Link :href="create()"><Plus class="mr-1 h-4 w-4" />New Product</Link>
                </Button>
            </div>

            <div class="flex gap-3">
                <div class="relative w-72">
                    <Search class="absolute top-2.5 left-3 h-4 w-4 text-muted-foreground" />
                    <Input v-model="search" placeholder="Search name, SKU, barcode…" class="pl-9" />
                </div>
                <Select v-model="categoryId">
                    <SelectTrigger class="w-48"><SelectValue placeholder="All categories" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All categories</SelectItem>
                        <SelectItem v-for="c in categories" :key="c.id" :value="String(c.id)">{{ c.name }}</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50 text-left">
                        <tr>
                            <th class="p-3">Product</th>
                            <th class="p-3">SKU</th>
                            <th class="p-3">Category</th>
                            <th class="p-3">Brand</th>
                            <th class="p-3 text-right">Price</th>
                            <th class="p-3 text-right">Stock</th>
                            <th class="p-3">Status</th>
                            <th class="p-3" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in products.data" :key="p.id" class="border-b last:border-0">
                            <td class="p-3 font-medium">
                                {{ p.name }}
                                <span v-if="p.variants.length" class="text-xs text-muted-foreground">
                                    · {{ p.variants.length }} variants
                                </span>
                            </td>
                            <td class="p-3 text-muted-foreground">{{ p.sku }}</td>
                            <td class="p-3">{{ p.category?.name ?? '—' }}</td>
                            <td class="p-3">{{ p.brand?.name ?? '—' }}</td>
                            <td class="p-3 text-right">{{ format(p.selling_price) }}</td>
                            <td class="p-3 text-right">
                                <span :class="Number(p.inventories_sum_quantity ?? 0) <= p.low_stock_threshold ? 'font-semibold text-destructive' : ''">
                                    {{ p.inventories_sum_quantity ?? 0 }}
                                </span>
                            </td>
                            <td class="p-3">
                                <Badge :variant="p.is_active ? 'secondary' : 'outline'">
                                    {{ p.is_active ? 'Active' : 'Inactive' }}
                                </Badge>
                            </td>
                            <td class="p-3">
                                <div class="flex justify-end gap-1">
                                    <Button v-if="can('products.update')" size="icon" variant="ghost" as-child>
                                        <Link :href="edit.url(p.id)"><Pencil class="h-4 w-4" /></Link>
                                    </Button>
                                    <Button v-if="can('products.delete')" size="icon" variant="ghost" @click="remove(p.id)">
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!products.data.length">
                            <td colspan="8" class="p-8 text-center text-muted-foreground">No products found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex gap-1">
                <Button
                    v-for="l in products.links"
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
