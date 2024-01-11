<script setup lang="ts">
import Accordion from "primevue/accordion";
import AccordionTab from "primevue/accordiontab";
import Dialog from "primevue/dialog";
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import Dropdown from "primevue/dropdown";
import InputError from "@/Components/Jetstream/InputError.vue";
import InputLabel from "@/Components/Jetstream/InputLabel.vue";
import InputSwitch from "primevue/inputswitch";
import TextInput from "@/Components/Jetstream/TextInput.vue";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import { decimal, integer, required, requiredIf } from "@vuelidate/validators";
import { CreateProject, GetProject, updateProject } from "@/Queries/Project";
import { onMounted, ref, watch } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { useToast } from "primevue/usetoast";
import { CreateProjectInterface, ProjectInterface } from "@/Types/Project";
import { isEmpty, isNull } from "lodash";
import { GetUsersByRole } from "@/Queries/User";
import { CreateOrder } from "@/Queries/Order";
import { DateTime } from "luxon";
import { orderReferenceNumber } from "@/Validators/Generic";
import { DropdownInterface } from "@/Types/Page";

const props = defineProps({
    showModal: {
        required: false,
        type: Number,
        default: 0,
    },
    projectId: {
        required: false,
        type: Number,
        default: null,
    },
});

const emits = defineEmits(["updated"]);
const projectForm = ref<CreateProjectInterface>({
    id: null,
    name: null,
    is_active: true,
    description: null,
    customer_id: null,
    project_manager_id: null,
    create_order: false,
    hours: 0,
    order_id: null,
});
const customers = ref<Array<DropdownInterface>>([]);
const projectManagers = ref<Array<DropdownInterface>>([]);
const projectRules = {
    name: { required },
    is_active: { required },
    customer_id: { required, integer },
    project_manager_id: { required, integer },
    create_order: { required },
    hours: {
        requiredIf: requiredIf((value, vm) => vm.create_order),
        decimal,
    },
    order_id: {
        requiredIf: requiredIf((value, vm) => vm.create_order),
        orderReferenceNumber,
    },
};
const modalTitle = ref<string>("Create Project");
const toast = useToast();
const isVisible = ref<boolean>(false);
const v$ = useVuelidate(projectRules, projectForm);

/**
 * Cancel the model
 */
const cancel = function () {
    isVisible.value = false;
    resetForm();
};
/**
 * Get customers for the dropdown
 */
const getData = async function () {
    try {
        let list = await GetUsersByRole("Customer");
        list.forEach((customer) => {
            customers.value.push({
                code: customer.id,
                name: customer.first_name + " " + customer.last_name,
            });
        });
    } catch (error) {
        customers.value = [];
    }
    try {
        let list = await GetUsersByRole("Project Manager");
        list.forEach((customer) => {
            projectManagers.value.push({
                code: customer.id,
                name: customer.first_name + " " + customer.last_name,
            });
        });
    } catch (error) {
        projectManagers.value = [];
    }
    modalTitle.value = "Create Project";
    if (!isNull(props.projectId)) {
        const project = await GetProject(props.projectId);
        modalTitle.value = `Update ${project.name}`;
        resetForm(project);
    }
};
/**
 * Reset the form & validation
 */
const resetForm = function (project?: ProjectInterface) {
    projectForm.value.id = project?.id ?? null;
    projectForm.value.name = project?.name ?? null;
    projectForm.value.is_active = project?.is_active ?? true;
    projectForm.value.customer_id = project?.customer?.id ?? null;
    projectForm.value.project_manager_id = project?.project_manager?.id ?? null;
    projectForm.value.create_order = false;
    projectForm.value.hours = 0;
    projectForm.value.order_id = null;
    v$.value.$reset();
};
/**
 * Save the form
 */
const save = async function () {
    await v$.value.$validate();
    if (v$.value.$errors.length) {
        return;
    }
    const { create_order, hours, order_id, ...data } = projectForm.value;
    try {
        let message = "Project Created.";
        if (isNull(projectForm.value.id)) {
            const project = await CreateProject(data);
            if (create_order) {
                await CreateOrder({
                    email: project.customer.email,
                    reference_number: order_id,
                    project_id: parseInt(project.id),
                    hours: hours,
                    date: DateTime.now().toFormat("yyyy-MM-dd"),
                });
            }
        } else {
            await updateProject(projectForm.value);
            message = "Project Updated.";
        }
        toast.add({
            severity: "success",
            summary: "Success",
            detail: message,
            life: 15000,
        });
        isVisible.value = false;
        emits("updated");
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: error,
            life: 15000,
        });
    }
};
/**
 * When the model is first loaded, get the projects
 */
onMounted(() => {
    getData();
});
/**
 * Watch for a changes
 * A change in the order id indicates if this is a new order or a current order
 */
watch(
    () => props.projectId,
    async (projectId: number | null) => {
        if (isEmpty(projectId)) {
            resetForm();
            return;
        }
        try {
            const project = await GetProject(props.projectId);
            resetForm(project);
        } catch (error) {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: error,
                life: 15000,
            });
            return;
        }
    },
);
watch(
    () => props.showModal,
    () => (isVisible.value = true),
);
</script>

<template>
    <Dialog
        :visible="isVisible"
        :style="{ width: '40vw' }"
        modal
        @update:visible="cancel"
    >
        <form id="editProject" @submit.prevent>
            <h3 class="text-3xl font-bold dark:text-white mb-2">
                {{ modalTitle }}
            </h3>
            <div class="m-3">
                <InputLabel for="name"
                    >{{ $t("form.project.name") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <TextInput
                    id="name"
                    ref="nameInput"
                    v-model.trim="projectForm.name"
                    type="text"
                    :class="{
                        'mt-1': true,
                        block: true,
                        'w-full': true,
                        'border-red-600': v$.name.$error,
                    }"
                />
                <InputError
                    message="This is required and may only contain letters, numbers."
                    class="mt-2"
                    :hidden="v$.name.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="is_active"
                    >{{ $t("form.project.active") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <InputSwitch v-model="projectForm.is_active" />
                <InputError
                    message="This is required ."
                    class="mt-2"
                    :hidden="v$.is_active.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="project_customer"
                    >{{ $t("user.roles.Customer") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <Dropdown
                    v-model="projectForm.customer_id"
                    :options="customers"
                    option-label="name"
                    option-value="code"
                    style="width: 100%"
                    :class="{
                        'border-red-600': v$.customer_id.$error,
                    }"
                />
                <InputError
                    message="This is required."
                    class="mt-2"
                    :hidden="v$.customer_id.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="project_manager"
                    >{{ $t("user.roles.Project Manager") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <Dropdown
                    v-model="projectForm.project_manager_id"
                    :options="projectManagers"
                    option-label="name"
                    option-value="code"
                    style="width: 100%"
                    :class="{
                        'border-red-600': v$.project_manager_id.$error,
                    }"
                />
                <InputError
                    message="This is required."
                    class="mt-2"
                    :hidden="v$.project_manager_id.$errors.length < 1"
                />
            </div>
            <Accordion v-if="isNull(projectForm.id)" class="m-3">
                <AccordionTab header="Hours">
                    <div class="m-3">
                        <label for="create_order">Create Order </label>
                        <InputSwitch
                            id="create_order"
                            v-model="projectForm.create_order"
                            class="align-middle"
                        />
                        <br />
                        <small
                            >If selected, this will create an order for this
                            project. The customer email will be used as the
                            order email.</small
                        >
                    </div>
                    <div class="m-3">
                        <InputLabel for="order_id"
                            >{{ $t("form.order.number") }}
                        </InputLabel>
                        <TextInput
                            id="order_id"
                            v-model.trim="projectForm.order_id"
                            :disabled="!projectForm.create_order"
                            type="text"
                            :class="{
                                'focus:outline-none disabled:opacity-50':
                                    !projectForm.create_order,
                                'mt-1': true,
                                block: true,
                                'border-red-600': v$.order_id.$error,
                            }"
                        />
                        <InputError
                            message="This is required and may only contain letters, numbers, dashes, and underscores."
                            class="mt-2"
                            :hidden="v$.order_id.$errors.length < 1"
                        />
                    </div>
                    <div class="m-3">
                        <InputLabel>{{ $t("form.order.hours") }}</InputLabel>
                        <TextInput
                            id="hours"
                            ref="hoursInput"
                            v-model.number="projectForm.hours"
                            :disabled="!projectForm.create_order"
                            type="number"
                            :class="{
                                'focus:outline-none disabled:opacity-50':
                                    !projectForm.create_order,
                                'mt-1': true,
                                block: true,
                                'border-red-600': v$.hours.$error,
                            }"
                        />
                    </div>
                </AccordionTab>
            </Accordion>
        </form>
        <template #footer>
            <SuccessButton class="mr-1" @click="save"> Save</SuccessButton>
            <DangerButton @click="cancel"> Cancel</DangerButton>
        </template>
    </Dialog>
</template>
