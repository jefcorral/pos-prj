<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Plus } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import { store, update } from '@/routes/users';

const props = defineProps<{
    users: {
        id: number;
        name: string;
        email: string;
        is_active: boolean;
        branch_id: number | null;
        roles: { name: string }[];
        branch: { name: string } | null;
    }[];
    roles: { id: number; name: string }[];
    branches: { id: number; name: string }[];
}>();

const dialogOpen = ref(false);
const editing = ref<number | null>(null);
const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'cashier',
    branch_id: 'none' as string,
    is_active: true,
});

function openEdit(u: (typeof props.users)[number]) {
    editing.value = u.id;
    form.name = u.name;
    form.email = u.email;
    form.password = '';
    form.role = u.roles[0]?.name ?? 'cashier';
    form.branch_id = u.branch_id ? String(u.branch_id) : 'none';
    form.is_active = u.is_active;
    dialogOpen.value = true;
}

function submit() {
    const payload = form.transform((d) => ({
        ...d,
        branch_id: d.branch_id !== 'none' ? d.branch_id : null,
    }));
    if (editing.value) {
        payload.put(update.url(editing.value), {
            onSuccess: () => (dialogOpen.value = false),
        });
    } else {
        payload.post(store.url(), {
            onSuccess: () => (dialogOpen.value = false),
        });
    }
}
</script>

<template>
    <Head title="Users" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Users</h1>
                <Button
                    @click="
                        editing = null;
                        form.reset();
                        dialogOpen = true;
                    "
                >
                    <Plus class="mr-1 h-4 w-4" />New User
                </Button>
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b text-left">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Role</th>
                            <th class="p-3">Branch</th>
                            <th class="p-3">Status</th>
                            <th class="p-3" />
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="u in users"
                            :key="u.id"
                            class="border-b last:border-0"
                        >
                            <td class="p-3 font-medium">{{ u.name }}</td>
                            <td class="p-3">{{ u.email }}</td>
                            <td class="p-3">
                                <Badge>{{ u.roles[0]?.name ?? '—' }}</Badge>
                            </td>
                            <td class="p-3">{{ u.branch?.name ?? '—' }}</td>
                            <td class="p-3">
                                <Badge
                                    :variant="
                                        u.is_active
                                            ? 'secondary'
                                            : 'destructive'
                                    "
                                >
                                    {{ u.is_active ? 'Active' : 'Disabled' }}
                                </Badge>
                            </td>
                            <td class="p-3 text-right">
                                <Button
                                    size="icon"
                                    variant="ghost"
                                    @click="openEdit(u)"
                                    ><Pencil class="h-4 w-4"
                                /></Button>
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
                        editing ? 'Edit User' : 'New User'
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
                        <Label>Email</Label
                        ><Input v-model="form.email" type="email" /><InputError
                            :message="form.errors.email"
                        />
                    </div>
                    <div>
                        <Label
                            >Password
                            {{ editing ? '(leave blank to keep)' : '' }}</Label
                        >
                        <Input v-model="form.password" type="password" />
                        <InputError :message="form.errors.password" />
                    </div>
                    <div>
                        <Label>Role</Label>
                        <Select v-model="form.role">
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="r in roles"
                                    :key="r.id"
                                    :value="r.name"
                                    >{{ r.name }}</SelectItem
                                >
                            </SelectContent>
                        </Select>
                    </div>
                    <div>
                        <Label>Branch</Label>
                        <Select v-model="form.branch_id">
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">None</SelectItem>
                                <SelectItem
                                    v-for="b in branches"
                                    :key="b.id"
                                    :value="String(b.id)"
                                    >{{ b.name }}</SelectItem
                                >
                            </SelectContent>
                        </Select>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <Checkbox v-model:checked="form.is_active" /> Active
                    </label>
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
