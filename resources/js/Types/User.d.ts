import { RoleInterface, RoleType } from "@/Types/Permission";

export type UserType = {
    id?: number | null;
    first_name?: string | null;
    last_name?: string | null;
    email?: string | null;
    roles?: Array<RoleType>;
    created_at?: string | null;
    updated_at?: string | null;
    role_id?: number | null;
};

export interface UserInterface {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    roles?: Array<RoleInterface>;
    created_at: string;
    updated_at: string;
    role_id?: number;
}

export interface UserWriteInterface {
    id?: number | null;
    first_name: string | null;
    last_name: string | null;
    email: string | null;
    roles?: Array<RoleInterface>;
    role_id?: number | null;
}
