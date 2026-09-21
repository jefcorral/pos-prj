<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { ref } from 'vue';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { store } from '@/routes/branches';

defineProps<{
    branches: {
        id: number;
        name: string;
        code: string;
        address: string | null;
        is_active: boolean;
        users_count: number;
    }[];
}>();

const dialogOpen = ref(false);
const form = useForm({ name: '', code: '', address: '', phone: '' });
</script>

<template>
    <Head title="Branches" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Branches</h1>
                <Button
                    @click="
                        form.reset();
                        dialogOpen = true;
                    "
                    ><Plus class="mr-1 h-4 w-4" />New Branch</Button
                >
            </div>

            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b text-left">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3">Code</th>
                            <th class="p-3">Address</th>
                            <th class="p-3 text-right">Users</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="b in branches"
                            :key="b.id"
                            class="border-b last:border-0"
                        >
                            <td class="p-3 font-medium">{{ b.name }}</td>
                            <td class="p-3 font-mono">{{ b.code }}</td>
                            <td class="p-3">{{ b.address ?? '—' }}</td>
                            <td class="p-3 text-right">{{ b.users_count }}</td>
                            <td class="p-3">
                                <Badge
                                    :variant="
                                        b.is_active
                                            ? 'secondary'
                                            : 'destructive'
                                    "
                                >
                                    {{ b.is_active ? 'Active' : 'Inactive' }}
                                </Badge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Dialog v-model:open="dialogOpen">
            <DialogContent class="max-w-md">
                <DialogHeader
                    ><DialogTitle>New Branch</DialogTitle></DialogHeader
                >
                <form
                    class="space-y-3"
                    @submit.prevent="
                        form.post(store.url(), {
                            onSuccess: () => (dialogOpen = false),
                        })
                    "
                >
                    <div>
                        <Label>Name</Label
                        ><Input v-model="form.name" /><InputError
                            :message="form.errors.name"
                        />
                    </div>
                    <div>
                        <Label>Code</Label
                        ><Input v-model="form.code" /><InputError
                            :message="form.errors.code"
                        />
                    </div>
                    <div>
                        <Label>Address</Label><Input v-model="form.address" />
                    </div>
                    <div>
                        <Label>Phone</Label><Input v-model="form.phone" />
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
