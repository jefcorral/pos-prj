<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Trash2 } from '@lucide/vue';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { store as storeBrand, destroy as destroyBrand } from '@/routes/catalog/brands';
import {
    store as storeCategory,
    destroy as destroyCategory,
} from '@/routes/catalog/categories';
import { store as storeDiscount } from '@/routes/catalog/discounts';
import { store as storeTax } from '@/routes/catalog/taxes';
import { store as storeUnit } from '@/routes/catalog/units';

defineProps<{
    categories: { id: number; name: string; products_count: number }[];
    brands: { id: number; name: string }[];
    units: { id: number; name: string; abbreviation: string }[];
    taxes: { id: number; name: string; rate: string; type: string }[];
    discounts: { id: number; name: string; type: string; value: string }[];
}>();

const categoryForm = useForm({ name: '' });
const brandForm = useForm({ name: '' });
const unitForm = useForm({ name: '', abbreviation: '' });
const taxForm = useForm({ name: '', rate: '', type: 'exclusive' });
const discountForm = useForm({ name: '', type: 'percent', value: '' });
</script>

<template>
    <Head title="Catalog" />
    <AppLayout>
        <div class="grid gap-4 p-4 md:grid-cols-2 xl:grid-cols-3">
            <Card>
                <CardHeader><CardTitle>Categories</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <form
                        class="flex gap-2"
                        @submit.prevent="categoryForm.post(storeCategory.url(), { onSuccess: () => categoryForm.reset() })"
                    >
                        <Input v-model="categoryForm.name" placeholder="New category" />
                        <Button type="submit" :disabled="categoryForm.processing">Add</Button>
                    </form>
                    <div v-for="c in categories" :key="c.id" class="flex items-center justify-between text-sm">
                        <span>{{ c.name }} <span class="text-muted-foreground">({{ c.products_count }})</span></span>
                        <Button size="icon" variant="ghost" @click="router.delete(destroyCategory.url(c.id))">
                            <Trash2 class="h-4 w-4 text-destructive" />
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Brands</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <form
                        class="flex gap-2"
                        @submit.prevent="brandForm.post(storeBrand.url(), { onSuccess: () => brandForm.reset() })"
                    >
                        <Input v-model="brandForm.name" placeholder="New brand" />
                        <Button type="submit" :disabled="brandForm.processing">Add</Button>
                    </form>
                    <div v-for="b in brands" :key="b.id" class="flex items-center justify-between text-sm">
                        <span>{{ b.name }}</span>
                        <Button size="icon" variant="ghost" @click="router.delete(destroyBrand.url(b.id))">
                            <Trash2 class="h-4 w-4 text-destructive" />
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Units</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <form
                        class="flex gap-2"
                        @submit.prevent="unitForm.post(storeUnit.url(), { onSuccess: () => unitForm.reset() })"
                    >
                        <Input v-model="unitForm.name" placeholder="Name" />
                        <Input v-model="unitForm.abbreviation" placeholder="Abbr." class="w-20" />
                        <Button type="submit" :disabled="unitForm.processing">Add</Button>
                    </form>
                    <div v-for="u in units" :key="u.id" class="text-sm">
                        {{ u.name }} ({{ u.abbreviation }})
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Taxes</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <form
                        class="flex gap-2"
                        @submit.prevent="taxForm.post(storeTax.url(), { onSuccess: () => taxForm.reset() })"
                    >
                        <Input v-model="taxForm.name" placeholder="Name" />
                        <Input v-model="taxForm.rate" type="number" step="0.01" placeholder="%" class="w-24" />
                        <Select v-model="taxForm.type">
                            <SelectTrigger class="w-32"><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="exclusive">Exclusive</SelectItem>
                                <SelectItem value="inclusive">Inclusive</SelectItem>
                            </SelectContent>
                        </Select>
                        <Button type="submit" :disabled="taxForm.processing">Add</Button>
                    </form>
                    <div v-for="t in taxes" :key="t.id" class="text-sm">
                        {{ t.name }} — {{ t.rate }}% ({{ t.type }})
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Discounts</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <form
                        class="flex gap-2"
                        @submit.prevent="discountForm.post(storeDiscount.url(), { onSuccess: () => discountForm.reset() })"
                    >
                        <Input v-model="discountForm.name" placeholder="Name" />
                        <Select v-model="discountForm.type">
                            <SelectTrigger class="w-28"><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="percent">%</SelectItem>
                                <SelectItem value="fixed">Fixed</SelectItem>
                            </SelectContent>
                        </Select>
                        <Input v-model="discountForm.value" type="number" step="0.01" placeholder="Value" class="w-24" />
                        <Button type="submit" :disabled="discountForm.processing">Add</Button>
                    </form>
                    <div v-for="d in discounts" :key="d.id" class="text-sm">
                        {{ d.name }} — {{ d.type === 'percent' ? `${d.value}%` : d.value }}
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
