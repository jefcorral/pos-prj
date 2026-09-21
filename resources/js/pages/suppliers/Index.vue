<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2 } from '@lucide/vue';
import { ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, index, store, update } from '@/routes/suppliers';

const confirmAction = (msg: string) => window.confirm(msg);

const props = defineProps<{
    suppliers: {
        data: {
            id: number;
            name: string;
            contact_name: string | null;
            phone: string | null;
            email: string | null;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();

const search = ref(props.filters.search ?? '');
watch(search, () => {
    router.get(
        index.url(),
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
});

const dialogOpen = ref(false);
const editing = ref<number | null>(null);
const form = useForm({
    name: '',
    contact_name: '',
    phone: '',
    email: '',
    address: '',
    is_active: true,
});

function openEdit(s: any) {
    editing.value = s.id;
    form.name = s.name;
    form.contact_name = s.contact_name ?? '';
    form.phone = s.phone ?? '';
    form.email = s.email ?? '';
    form.address = '';
    dialogOpen.value = true;
}

function submit() {
    if (editing.value) {
        form.put(update.url(editing.value), {
            onSuccess: () => (dialogOpen.value = false),
        });
    } else {
        form.post(store.url(), { onSuccess: () => (dialogOpen.value = false) });
    }
}
</script>

<template>
    <Head title="Suppliers" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Suppliers</h1>
                <Button
                    @click="
                        editing = null;
                        form.reset();
                        dialogOpen = true;
                    "
                >
                    <Plus class="mr-1 h-4 w-4" />New Supplier
                </Button>
            </div>

            <div class="relative w-72">
                <Search
                    class="text-muted-foreground absolute top-2.5 left-3 h-4 w-4"
                />
                <Input v-model="search" placeholder="Search…" class="pl-9" />
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b text-left">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3">Contact</th>
                            <th class="p-3">Phone</th>
                            <th class="p-3">Email</th>
                            <th class="p-3" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="s in suppliers.data"
                            :key="s.id"
                            class="border-b last:border-0"
                        >
                            <td class="p-3 font-medium">{{ s.name }}</td>
                            <td class="p-3">{{ s.contact_name ?? '—' }}</td>
                            <td class="p-3">{{ s.phone ?? '—' }}</td>
                            <td class="p-3">{{ s.email ?? '—' }}</td>
                            <td class="p-3">
                                <div class="flex justify-end gap-1">
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        @click="openEdit(s)"
                                        ><Pencil class="h-4 w-4"
                                    /></Button>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        @click="
                                            confirmAction('Delete supplier?') &&
                                            router.delete(destroy.url(s.id))
                                        "
                                    >
                                        <Trash2
                                            class="text-destructive h-4 w-4"
                                        />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!suppliers.data.length">
                            <td
                                colspan="5"
                                class="text-muted-foreground p-8 text-center"
                            >
                                No suppliers.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Dialog v-model:open="dialogOpen">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>{{
                        editing ? 'Edit Supplier' : 'New Supplier'
                    }}</DialogTitle>
                </DialogHeader>
                <form class="space-y-3" @submit.prevent="submit">
                    <div>
                        <Label>Name</Label
                        ><Input v-model="form.name" /><InputError
                            :message="form.errors.name"
                        />
                    </div>
                    <div>
                        <Label>Contact person</Label
                        ><Input v-model="form.contact_name" />
                    </div>
                    <div>
                        <Label>Phone</Label><Input v-model="form.phone" />
                    </div>
                    <div>
                        <Label>Email</Label
                        ><Input v-model="form.email" type="email" />
                    </div>
                    <div>
                        <Label>Address</Label><Input v-model="form.address" />
                    </div>
                    <DialogFooter
                        ><Button type="submit" :disabled="form.processing"
                            >Save</Button
                        ></DialogFooter
                    >
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
