import axios from "axios";
import { UserInterface, UserWriteInterface } from "@/Types/User";
import { PaginationInterface } from "@/Types/Query";
import { isEmpty } from "lodash";

export interface UsersResponseInterface {
    data: Array<UserInterface>;
    paginatorInfo: PaginationInterface;
}

export interface UserFilterInterface {
    email?: string | null;
    first_name?: string | null;
    last_name?: string | null;
    role?: number | null;
}

/**
 * Create a user
 * @param data<UserInterface>
 * @constructor
 */
const CreateUser = async function (
    data: UserWriteInterface,
): Promise<UserInterface> {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { id, role_id, ...userData } = data;
    const {
        data: { data: { userCreate = {} } = { userCreate: {} }, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($input: UserCreateInput!) {
                userCreate(input: $input) {
                    id
                    first_name
                    last_name
                    email
                    created_at
                    updated_at
                }
            }`,
            variables: {
                input: userData,
            },
        },
    });
    if (errors?.length > 0) {
        throw new Error(errors[0].message);
    }
    return userCreate;
};
/**
 * Delete an order
 * @param userId<number>
 */
const DeleteUser = async function (userId: number): Promise<number> {
    const {
        data: { userDelete, errors = [] },
    } = await axios({
        // const result = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($user: ID!) {
                userDelete(id: $user) {
                    id
                    first_name
                    last_name
                    email
                    created_at
                    updated_at
                }
            }`,
            variables: {
                user: userId,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return userDelete;
};
/**
 * Get a user
 * @param userId<number>
 * @constructor
 */
const GetUser = async function (userId: number): Promise<UserInterface> {
    const {
        data: { data: { user = {} } = { user: {} }, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            query($input: UserInput!) {
                user(input: $input) {
                    id
                    first_name
                    last_name
                    email
                    created_at
                    updated_at
                    roles {
                        id
                        name
                    }
                }
            }`,
            variables: {
                input: { id: userId },
            },
        },
    });
    if (errors?.length > 0) {
        throw new Error(errors[0].message);
    }
    return user;
};
/**
 * Get all users that have a specific role
 * @param role
 */
const GetUsersByRole = async function (
    role: string,
): Promise<Array<UserInterface>> {
    const {
        data: {
            data: { users: { data = [] } } = { users: { data: [] } },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            query ($hasRoles: QueryUsersHasRolesWhereHasConditions) {
                users(first: 10000, hasRoles: $hasRoles) {
                    data {
                        id
                        first_name
                        last_name
                        email
                    }
                }
            }`,
            variables: {
                hasRoles: { column: "NAME", operator: "EQ", value: role },
            },
        },
    });
    if (errors?.length > 0) {
        throw new Error(errors[0].message);
    }
    return data;
};
/**
 * Get a list of users
 */
const GetUsersList = async function (
    first: number = 10,
    page: number = 1,
    orderBy: string = "EMAIL",
    orderByDirection: string = "ASC",
    filterBy: UserFilterInterface = {},
): Promise<UsersResponseInterface> {
    const orderByClause = [{ column: orderBy, order: orderByDirection }];
    let userSearchInput = {};
    let hasRoles = {};
    if (!isEmpty(filterBy?.email)) {
        userSearchInput = { ...userSearchInput, email: `%${filterBy.email}%` };
    }
    if (!isEmpty(filterBy?.first_name)) {
        userSearchInput = {
            ...userSearchInput,
            first_name: `%${filterBy.first_name}%`,
        };
    }
    if (!isEmpty(filterBy?.last_name)) {
        userSearchInput = {
            ...userSearchInput,
            last_name: `%${filterBy.last_name}%`,
        };
    }
    if (!isEmpty(filterBy?.role)) {
        hasRoles = {
            column: "ID",
            operator: "EQ",
            value: filterBy.role,
        };
    }
    const {
        data: { data: { users = [] } = { users: [] }, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            query($first: Int!, $page: Int!, $orderBy: [QueryUsersOrderByOrderByClause!], $input: UserSearchInput, $hasRoles: QueryUsersHasRolesWhereHasConditions) {
                users(first: $first, page: $page, orderBy: $orderBy, input: $input, hasRoles: $hasRoles) {
                   paginatorInfo {
                        count
                        currentPage
                        firstItem
                        hasMorePages
                        lastItem
                        lastPage
                        perPage
                        total
                    }
                    data {
                        id
                        first_name
                        last_name
                        email
                        created_at
                        updated_at
                        roles {
                            name
                        }
                    }
                }
            }`,
            variables: {
                first: first,
                page: page,
                orderBy: orderByClause,
                input: userSearchInput,
                hasRoles: hasRoles,
            },
        },
    });
    if (errors?.length > 0) {
        throw new Error(errors[0].message);
    }
    return users;
};

/**
 * Update a user
 * @param update<UserInterface>
 * @constructor
 */
const UpdateUser = async function (
    update: UserWriteInterface,
): Promise<UserInterface> {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { role_id, ...user } = update;
    const {
        data: { data: { userUpdate = {} } = { userUpdate: {} }, errors = [] },
    } = await axios({
        url: "graphql",
        method: "post",
        data: {
            query: `
            mutation($input: UserUpdateInput!) {
                userUpdate(input: $input) {
                    id
                    first_name
                    last_name
                    email
                    created_at
                    updated_at
                }
            }`,
            variables: {
                input: user,
            },
        },
    });
    if (errors?.length > 0) {
        throw new Error(errors[0].message);
    }
    return userUpdate;
};

export {
    CreateUser,
    DeleteUser,
    GetUser,
    GetUsersByRole,
    GetUsersList,
    UpdateUser,
};
