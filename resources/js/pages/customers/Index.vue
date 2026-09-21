<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2 } from '@lucide/vue';
import { ref, watch } from 'vue';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, index, store, update } from '@/routes/customers';

const props = defineProps<{
    customers: {
        data: {
            id: number;
            name: string;
            phone: string | null;
            email: string | null;
            type: string;
            sales_count: number;
            is_active: boolean;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();

const search = ref(props.filters.search ?? '');
watch(search, () => {
    router.get(index.url(), { search: search.value || undefined }, { preserveState: true, replace: true });
});

const dialogOpen = ref(false);
const editing = ref<number | null>(null);
const form = useForm({ name: '', phone: '', email: '', address: '', type: 'regular', is_active: true });

function openCreate() {
    editing.value = null;
    form.reset();
    dialogOpen.value = true;
}

function openEdit(c: any) {
    editing.value = c.id;
    form.name = c.name;
    form.phone = c.phone ?? '';
    form.email = c.email ?? '';
    form.address = '';
    form.type = c.type;
    form.is_active = c.is_active;
    dialogOpen.value = true;
}

function submit() {
    if (editing.value) {
        form.put(update.url(editing.value), { onSuccess: () => (dialogOpen.value = false) });
    } else {
        form.post(store.url(), { onSuccess: () => (dialogOpen.value = false) });
    }
}
</script>

<template>
    <Head title="Customers" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Customers</h1>
                <Button @click="openCreate"><Plus class="mr-1 h-4 w-4" />New Customer</Button>
            </div>

            <div class="relative w-72">
                <Search class="absolute top-2.5 left-3 h-4 w-4 text-muted-foreground" />
                <Input v-model="search" placeholder="Search…" class="pl-9" />
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50 text-left">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3">Phone</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Type</th>
                            <th class="p-3 text-right">Purchases</th>
                            <th class="p-3" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in customers.data" :key="c.id" class="border-b last:border-0">
                            <td class="p-3 font-medium">{{ c.name }}</td>
                            <td class="p-3">{{ c.phone ?? '—' }}</td>
                            <td class="p-3">{{ c.email ?? '—' }}</td>
                            <td class="p-3"><Badge variant="outline">{{ c.type }}</Badge></td>
                            <td class="p-3 text-right">{{ c.sales_count }}</td>
                            <td class="p-3">
                                <div class="flex justify-end gap-1">
                                    <Button size="icon" variant="ghost" @click="openEdit(c)"><Pencil class="h-4 w-4" /></Button>
                                    <Button size="icon" variant="ghost" @click="confirm('Delete customer?') && router.delete(destroy.url(c.id))">
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!customers.data.length">
                            <td colspan="6" class="p-8 text-center text-muted-foreground">No customers.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Dialog v-model:open="dialogOpen">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ editing ? 'Edit Customer' : 'New Customer' }}</DialogTitle>
                </DialogHeader>
                <form class="space-y-3" @submit.prevent="submit">
                    <div>
                        <Label>Name</Label>
                        <Input v-model="form.name" />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div>
                        <Label>Phone</Label>
                        <Input v-model="form.phone" />
                    </div>
                    <div>
                        <Label>Email</Label>
                        <Input v-model="form.email" type="email" />
                        <InputError :message="form.errors.email" />
                    </div>
                    <div>
                        <Label>Address</Label>
                        <Input v-model="form.address" />
                    </div>
                    <div>
                        <Label>Type</Label>
                        <Select v-model="form.type">
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="regular">Regular</SelectItem>
                                <SelectItem value="wholesale">Wholesale</SelectItem>
                                <SelectItem value="vip">VIP</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <DialogFooter>
                        <Button type="submit" :disabled="form.processing">Save</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
