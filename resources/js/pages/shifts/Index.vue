<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Lock, LockOpen, Plus, Minus } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useMoney } from '@/composables/useMoney';
import { usePermissions } from '@/composables/usePermissions';
import AppLayout from '@/layouts/AppLayout.vue';
import { cash, close, open } from '@/routes/shifts';

const props = defineProps<{
    shifts: {
        data: {
            id: number;
            status: string;
            opening_cash: string;
            expected_cash: string | null;
            actual_cash: string | null;
            variance: string | null;
            opened_at: string;
            closed_at: string | null;
            user: { name: string } | null;
            branch: { name: string } | null;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    currentShift: {
        id: number;
        opening_cash: string;
        opened_at: string;
        cash_movements: { id: number; type: string; amount: string; reason: string | null; created_at: string }[];
    } | null;
}>();

const { format } = useMoney();
const { can } = usePermissions();

const openForm = useForm({ opening_cash: 0, note: '' });
const closeForm = useForm({ actual_cash: 0, note: '' });
const cashForm = useForm({ type: 'in', amount: 0, reason: '' });

const openDialog = ref(false);
const closeDialog = ref(false);
const cashDialog = ref(false);
</script>

<template>
    <Head title="Shifts" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <h1 class="text-xl font-bold">Cashier Shifts</h1>

            <Card v-if="currentShift">
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>
                        Current shift #{{ currentShift.id }}
                        <Badge class="ml-2">open</Badge>
                    </CardTitle>
                    <div class="flex gap-2">
                        <Button v-if="can('shifts.cash')" variant="outline" size="sm" @click="cashDialog = true">
                            Cash In/Out
                        </Button>
                        <Button size="sm" variant="destructive" @click="closeDialog = true">
                            <Lock class="mr-1 h-4 w-4" />Close Shift
                        </Button>
                    </div>
                </CardHeader>
                <CardContent class="text-sm">
                    <div class="mb-3">
                        Opened {{ new Date(currentShift.opened_at).toLocaleString() }} ·
                        Opening cash {{ format(currentShift.opening_cash) }}
                    </div>
                    <table class="w-full">
                        <tbody>
                            <tr v-for="m in currentShift.cash_movements" :key="m.id" class="border-b last:border-0">
                                <td class="py-1 text-muted-foreground">{{ new Date(m.created_at).toLocaleTimeString() }}</td>
                                <td class="py-1 uppercase">{{ m.type }}</td>
                                <td class="py-1">{{ m.reason ?? '—' }}</td>
                                <td class="py-1 text-right" :class="Number(m.amount) < 0 ? 'text-destructive' : 'text-green-600'">
                                    {{ format(m.amount) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <Card v-else>
                <CardContent class="flex items-center justify-between p-6">
                    <p class="text-muted-foreground">No open shift.</p>
                    <Button @click="openDialog = true"><LockOpen class="mr-1 h-4 w-4" />Open Shift</Button>
                </CardContent>
            </Card>

            <div class="rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50 text-left">
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">Cashier</th>
                            <th class="p-3">Branch</th>
                            <th class="p-3">Opened</th>
                            <th class="p-3">Closed</th>
                            <th class="p-3 text-right">Opening</th>
                            <th class="p-3 text-right">Expected</th>
                            <th class="p-3 text-right">Actual</th>
                            <th class="p-3 text-right">Variance</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="s in shifts.data" :key="s.id" class="border-b last:border-0">
                            <td class="p-3 font-mono">{{ s.id }}</td>
                            <td class="p-3">{{ s.user?.name }}</td>
                            <td class="p-3">{{ s.branch?.name }}</td>
                            <td class="p-3 text-muted-foreground">{{ new Date(s.opened_at).toLocaleString() }}</td>
                            <td class="p-3 text-muted-foreground">{{ s.closed_at ? new Date(s.closed_at).toLocaleString() : '—' }}</td>
                            <td class="p-3 text-right">{{ format(s.opening_cash) }}</td>
                            <td class="p-3 text-right">{{ s.expected_cash ? format(s.expected_cash) : '—' }}</td>
                            <td class="p-3 text-right">{{ s.actual_cash ? format(s.actual_cash) : '—' }}</td>
                            <td
                                class="p-3 text-right font-medium"
                                :class="s.variance && Number(s.variance) !== 0 ? 'text-destructive' : ''"
                            >
                                {{ s.variance ? format(s.variance) : '—' }}
                            </td>
                            <td class="p-3"><Badge :variant="s.status === 'open' ? 'default' : 'secondary'">{{ s.status }}</Badge></td>
                        </tr>
                        <tr v-if="!shifts.data.length">
                            <td colspan="10" class="p-8 text-center text-muted-foreground">No shifts yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Dialog v-model:open="openDialog">
            <DialogContent class="max-w-sm">
                <DialogHeader><DialogTitle>Open Shift</DialogTitle></DialogHeader>
                <form class="space-y-3" @submit.prevent="openForm.post(open.url(), { onSuccess: () => (openDialog = false) })">
                    <div>
                        <Label>Opening cash</Label>
                        <Input v-model.number="openForm.opening_cash" type="number" step="0.01" min="0" />
                        <InputError :message="openForm.errors.opening_cash" />
                    </div>
                    <DialogFooter><Button type="submit" :disabled="openForm.processing">Open</Button></DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="closeDialog">
            <DialogContent class="max-w-sm">
                <DialogHeader><DialogTitle>Close Shift</DialogTitle></DialogHeader>
                <form
                    class="space-y-3"
                    @submit.prevent="currentShift && closeForm.post(close.url(currentShift.id), { onSuccess: () => (closeDialog = false) })"
                >
                    <div>
                        <Label>Actual cash counted</Label>
                        <Input v-model.number="closeForm.actual_cash" type="number" step="0.01" min="0" />
                        <InputError :message="closeForm.errors.actual_cash" />
                    </div>
                    <div>
                        <Label>Note</Label>
                        <Input v-model="closeForm.note" />
                    </div>
                    <DialogFooter><Button type="submit" variant="destructive" :disabled="closeForm.processing">Close Shift</Button></DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="cashDialog">
            <DialogContent class="max-w-sm">
                <DialogHeader><DialogTitle>Cash In / Out</DialogTitle></DialogHeader>
                <form
                    class="space-y-3"
                    @submit.prevent="currentShift && cashForm.post(cash.url(currentShift.id), { onSuccess: () => { cashDialog = false; cashForm.reset(); } })"
                >
                    <div class="flex gap-2">
                        <Button type="button" :variant="cashForm.type === 'in' ? 'default' : 'outline'" class="flex-1" @click="cashForm.type = 'in'">
                            <Plus class="mr-1 h-4 w-4" />Cash In
                        </Button>
                        <Button type="button" :variant="cashForm.type === 'out' ? 'default' : 'outline'" class="flex-1" @click="cashForm.type = 'out'">
                            <Minus class="mr-1 h-4 w-4" />Cash Out
                        </Button>
                    </div>
                    <div>
                        <Label>Amount</Label>
                        <Input v-model.number="cashForm.amount" type="number" step="0.01" min="0.01" />
                        <InputError :message="cashForm.errors.amount" />
                    </div>
                    <div>
                        <Label>Reason</Label>
                        <Input v-model="cashForm.reason" />
                        <InputError :message="cashForm.errors.reason" />
                    </div>
                    <DialogFooter><Button type="submit" :disabled="cashForm.processing">Record</Button></DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
