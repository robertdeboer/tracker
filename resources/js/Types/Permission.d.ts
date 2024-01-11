export type RoleType = {
    id: number;
    name: string;
    guard_name: string;
};

export interface RoleInterface {
    id: number;
    name: string;
    guard_name: string;
}

export interface RoleOptionsInterface {
    code: number,
    name: string
}
