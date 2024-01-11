import { ProjectInterface, ProjectType } from "@/Types/Project";
import { UserInterface, UserType } from "@/Types/User";
import { TimeEntryInterface, TimeEntryType } from "@/Types/TimeEntry";

export type WorkItemTicketData = {
    id?: string;
    url?: string;
};

export type WorkItemType = {
    id: number;
    project: ProjectType;
    owner: UserType;
    name: string;
    is_open: boolean;
    start_date: string;
    end_date: string | null;
    ticket_data: WorkItemTicketData;
    users: Array<UserType>;
    time_entries: Array<TimeEntryType>;
    created_at: string;
    updated_at: string;
    hours?: number | null;
};

export interface WorkItemTicketDataInterface {
    id?: string;
    url?: string;
}

export interface WorkItemInterface {
    id: number;
    project: ProjectInterface;
    owner: UserInterface;
    name: string;
    is_open: boolean;
    start_date: string;
    end_date: string | null;
    ticket_data: WorkItemTicketDataInterface;
    time_entries?: Array<TimeEntryInterface>;
    created_at: string;
    updated_at: string;
    hours?: number | null;
    users?: Array<UserInterface> | null;
}

export interface WorkItemTicketDataWriteInterface {
    id: string | null;
    url: string | null;
}

export interface WorkItemWriteInterface {
    id?: number | null;
    name: string | null;
    is_open: boolean;
    start_date: string;
    end_date: string | null;
    project_id: number | null;
    owner_id: number | null;
    ticket_data: WorkItemTicketDataWriteInterface;
}
