import axios from "axios";
import { RoleInterface } from "@/Types/Permission";

/**
 * Get all roles in an array suitable for option fields
 */
const getRoleOptions = async function (): Promise<Array<RoleInterface>> {
    const {
        data: { data: { roles = {} } = { roles: [] }, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            query {
                roles {
                    code: id
                    name
                }
            }`,
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return roles;
};

/**
 * Assign a role to a user
 * @param userId
 * @param roleId
 */
const assingRoleToUser = async function (
    userId: number,
    roleId: number,
): Promise<number> {
    const {
        data: {
            data: { assignRoleToUser: { id } } = {
                assignRoleToUser: { id: null },
            },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($user: ID!, $role: ID!) {
                assignRoleToUser(user_id: $user, role_id: $role) {
                    id
                }
            }`,
            variables: {
                user: userId,
                role: roleId,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return id;
};

export { assingRoleToUser, getRoleOptions };
