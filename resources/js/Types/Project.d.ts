import { UserInterface } from "@/Types/User";
import { OrderInterface } from "@/Types/Order";
import { WorkItemInterface } from "@/Types/WorkItem";

export type ProjectType = {
    id: string;
    name: string;
    is_active: boolean;
    customer: UserInterface;
    project_manager: UserInterface;
    description?: string | null;
    orders?: Array<OrderInterface>;
    created_at: string;
    updated_at: string;
    hours_ordered?: number | null;
    hours_used?: number | null;
    work_items?: Array<WorkItemInterface>;
};

export interface ProjectInterface {
    id: number;
    name: string;
    is_active: boolean;
    customer: UserInterface;
    project_manager: UserInterface;
    description?: string | null;
    orders?: Array<OrderInterface>;
    created_at: string;
    updated_at: string;
    hours_ordered?: number | null;
    hours_used?: number | null;
    work_items?: Array<WorkItemInterface>;
}

export interface ProjectWriteInterface {
    id: string | number | null;
    name: string | null;
    is_active: boolean | null;
    description: string | null;
    customer_id: number | null;
    project_manager_id: number | null;
}

export interface CreateProjectInterface extends ProjectWriteInterface {
    create_order: boolean;
    hours: number | null;
    order_id: string | null;
}

export interface ProjectOptionsListInterface {
    id?: number | null;
    code?: string | null;
}
