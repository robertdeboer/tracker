import { WorkItemInterface, WorkItemType } from "@/Types/WorkItem";
import { UserInterface, UserType } from "@/Types/User";

export type TimeEntryType = {
    id: number;
    work_item: WorkItemType;
    author: UserType;
    hours: number;
    date: string;
    note: string;
    created_at: string;
    updated_at: string;
};

export interface TimeEntryInterface {
    id: number;
    work_item: WorkItemInterface;
    author: UserInterface;
    hours: number;
    date: string;
    note: string;
    created_at: string;
    updated_at: string;
}

export interface TimeEntryWriteInterface {
    id?: number | null;
    work_item_id?: number;
    author_id?: number | null;
    hours: number | null;
    date: string;
    note: string | null;
}
