<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus, X } from '@lucide/vue';
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
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, store, update } from '@/routes/products';

const props = defineProps<{
    product: {
        id: number;
        name: string;
        sku: string | null;
        barcode: string | null;
        description: string | null;
        category_id: number | null;
        brand_id: number | null;
        unit_id: number | null;
        tax_id: number | null;
        cost_price: string;
        selling_price: string;
        low_stock_threshold: number;
        track_stock: boolean;
        is_active: boolean;
        variants: {
            id?: number;
            name: string;
            sku: string | null;
            barcode: string | null;
            selling_price: string | null;
            cost_price: string | null;
        }[];
    } | null;
    categories: { id: number; name: string }[];
    brands: { id: number; name: string }[];
    units: { id: number; name: string; abbreviation: string }[];
    taxes: { id: number; name: string; rate: string }[];
}>();

const form = useForm({
    name: props.product?.name ?? '',
    sku: props.product?.sku ?? '',
    barcode: props.product?.barcode ?? '',
    description: props.product?.description ?? '',
    category_id: props.product?.category_id ? String(props.product.category_id) : 'none',
    brand_id: props.product?.brand_id ? String(props.product.brand_id) : 'none',
    unit_id: props.product?.unit_id ? String(props.product.unit_id) : 'none',
    tax_id: props.product?.tax_id ? String(props.product.tax_id) : 'none',
    cost_price: props.product?.cost_price ?? '0',
    selling_price: props.product?.selling_price ?? '0',
    low_stock_threshold: props.product?.low_stock_threshold ?? 0,
    track_stock: props.product?.track_stock ?? true,
    is_active: props.product?.is_active ?? true,
    image: null as File | null,
    variants: (props.product?.variants ?? []).map((v) => ({ ...v })) as any[],
});

function submit() {
    const payload = form.transform((d) => ({
        ...d,
        category_id: d.category_id !== 'none' ? d.category_id : null,
        brand_id: d.brand_id !== 'none' ? d.brand_id : null,
        unit_id: d.unit_id !== 'none' ? d.unit_id : null,
        tax_id: d.tax_id !== 'none' ? d.tax_id : null,
    }));

    if (props.product) {
        payload.transform((d) => ({ ...d, _method: 'put' })).post(
            update.url(props.product.id),
            { forceFormData: true },
        );
    } else {
        payload.post(store.url(), { forceFormData: true });
    }
}
</script>

<template>
    <Head :title="product ? 'Edit Product' : 'New Product'" />
    <AppLayout>
        <form class="space-y-4 p-4" @submit.prevent="submit">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">
                    {{ product ? `Edit ${product.name}` : 'New Product' }}
                </h1>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="index()">Cancel</Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing">Save</Button>
                </div>
            </div>

            <Card>
                <CardHeader><CardTitle>Details</CardTitle></CardHeader>
                <CardContent class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <Label>Name</Label>
                        <Input v-model="form.name" />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div>
                        <Label>SKU</Label>
                        <Input v-model="form.sku" />
                        <InputError :message="form.errors.sku" />
                    </div>
                    <div>
                        <Label>Barcode</Label>
                        <Input v-model="form.barcode" />
                        <InputError :message="form.errors.barcode" />
                    </div>
                    <div class="col-span-2">
                        <Label>Description</Label>
                        <Input v-model="form.description" />
                    </div>
                    <div>
                        <Label>Category</Label>
                        <Select v-model="form.category_id">
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">None</SelectItem>
                                <SelectItem v-for="c in categories" :key="c.id" :value="String(c.id)">{{ c.name }}</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div>
                        <Label>Brand</Label>
                        <Select v-model="form.brand_id">
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">None</SelectItem>
                                <SelectItem v-for="b in brands" :key="b.id" :value="String(b.id)">{{ b.name }}</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div>
                        <Label>Unit</Label>
                        <Select v-model="form.unit_id">
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">None</SelectItem>
                                <SelectItem v-for="u in units" :key="u.id" :value="String(u.id)">{{ u.name }} ({{ u.abbreviation }})</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div>
                        <Label>Tax</Label>
                        <Select v-model="form.tax_id">
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">None</SelectItem>
                                <SelectItem v-for="t in taxes" :key="t.id" :value="String(t.id)">{{ t.name }}</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Pricing & Stock</CardTitle></CardHeader>
                <CardContent class="grid grid-cols-3 gap-4">
                    <div>
                        <Label>Cost price</Label>
                        <Input v-model="form.cost_price" type="number" step="0.01" min="0" />
                        <InputError :message="form.errors.cost_price" />
                    </div>
                    <div>
                        <Label>Selling price</Label>
                        <Input v-model="form.selling_price" type="number" step="0.01" min="0" />
                        <InputError :message="form.errors.selling_price" />
                    </div>
                    <div>
                        <Label>Low-stock threshold</Label>
                        <Input v-model.number="form.low_stock_threshold" type="number" min="0" />
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox v-model:checked="form.track_stock" id="track" />
                        <Label for="track">Track stock</Label>
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox v-model:checked="form.is_active" id="active" />
                        <Label for="active">Active</Label>
                    </div>
                    <div>
                        <Label>Image</Label>
                        <Input type="file" accept="image/*" @input="form.image = ($event.target as HTMLInputElement).files?.[0] ?? null" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Variants</CardTitle>
                    <Button
                        type="button"
                        size="sm"
                        variant="outline"
                        @click="form.variants.push({ name: '', sku: '', barcode: '', selling_price: '', cost_price: '' })"
                    >
                        <Plus class="mr-1 h-4 w-4" />Add variant
                    </Button>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div v-for="(v, i) in form.variants" :key="i" class="flex items-center gap-2">
                        <Input v-model="v.name" placeholder="Name (e.g. Large / Red)" class="flex-1" />
                        <Input v-model="v.sku" placeholder="SKU" class="w-32" />
                        <Input v-model="v.barcode" placeholder="Barcode" class="w-36" />
                        <Input v-model="v.selling_price" type="number" step="0.01" placeholder="Price" class="w-28" />
                        <Input v-model="v.cost_price" type="number" step="0.01" placeholder="Cost" class="w-28" />
                        <Button type="button" size="icon" variant="ghost" @click="form.variants.splice(i, 1)">
                            <X class="h-4 w-4" />
                        </Button>
                    </div>
                    <p v-if="!form.variants.length" class="text-sm text-muted-foreground">
                        No variants. Leave price/cost empty on variants to inherit the product's.
                    </p>
                </CardContent>
            </Card>
        </form>
    </AppLayout>
</template>
