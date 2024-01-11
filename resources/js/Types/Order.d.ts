import { ProjectInterface, ProjectType } from "@/Types/Project";
import { WorkItemType } from "@/Types/WorkItem";

export type OrderType = {
    id: number;
    reference_number: string;
    date: string;
    email: string;
    hours: number;
    project?: ProjectType | null;
    work_items?: Array<WorkItemType> | null;
    created_at?: string | null;
    updated_at?: string | null;
    project_id?: number | null;
};

export interface OrderInterface {
    id: number;
    reference_number: string;
    date: string;
    email: string;
    hours: number;
    project: ProjectInterface | null;
}

export interface OrderWriteInterface {
    id?: number | null;
    reference_number?: string | null;
    date: string;
    email?: string | null;
    hours: number;
    project?: ProjectInterface | null;
    project_id: number | string | null;
}
