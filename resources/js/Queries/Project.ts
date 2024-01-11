import axios from "axios";
import { ProjectInterface, ProjectWriteInterface } from "@/Types/Project";
import {
    ComplexWhereInterface,
    OrderByInterface,
    PaginationInterface,
    WhereInterface,
} from "@/Types/Query";
import { UserInterface } from "@/Types/User";
import { isEmpty } from "lodash";

export interface ProjectOptionListInterface {
    code: number;
    name: string;
}

export interface ProjectUserInterface {
    data: Array<UserInterface>;
    paginatorInfo: PaginationInterface;
}

export interface ProjectUserFilterInterface {
    email?: string | null;
    first_name?: string | null;
    last_name?: string | null;
    role?: number | null;
}

/**
 * Create a project
 * @param newProject<ProjectInterface>
 * @constructor
 */
const CreateProject = async function (
    newProject: ProjectWriteInterface,
): Promise<ProjectInterface> {
    const {
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
        id,
        project_manager_id,
        customer_id,
        ...project
    } = newProject;
    const {
        data: {
            data: { projectCreate },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($input: ProjectCreateInput!) {
                projectCreate(
                    input: $input
                ) {
                    id
                    name
                    is_active
                    description
                    created_at
                    updated_at
                    customer {
                        id
                        email
                    }
                    project_manager {
                        id
                        email
                    }
                }
            }`,
            variables: {
                input: {
                    ...project,
                    customer: { connect: customer_id },
                    project_manager: { connect: project_manager_id },
                },
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return projectCreate;
};
/**
 * Delete a project
 *
 * @param projectId<number>
 * @constructor
 */
const DeleteProject = async function (
    projectId: number,
): Promise<ProjectInterface> {
    const {
        data: { errors = [], projectDelete = {} },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `mutation {
                projectDelete(id: ${projectId}) {
                    id
                }
            }`,
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return projectDelete;
};
/**
 * Get a project
 * @param projectId
 * @constructor
 */
const GetProject = async function (
    projectId: number | string,
): Promise<ProjectInterface> {
    const {
        data: { data: { project } = { project: {} }, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            query($input: ProjectInput!) {
                project(input: $input) {
                    id
                    name
                    is_active
                    description
                    created_at
                    updated_at
                    customer {
                        id
                        first_name
                        last_name
                        email
                    }
                    project_manager {
                        id
                        first_name
                        last_name
                        email
                    }
                    hours_ordered
                    hours_used
                }
            }`,
            variables: {
                input: { id: projectId },
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return project;
};
/**
 * Get the project log
 * @param id<number>
 */
const getProjectLog = async function (id: number): Promise<string> {
    const { data } = await axios({
        url: `/project/${id}/log`,
        method: "get",
    });
    return data;
};
/**
 * Get the projects in a list suitable for select boxes, etc
 * @constructor
 */
const GetProjectNamesOptionList = async function (
    activeOnly: boolean = true,
): Promise<Array<ProjectOptionListInterface>> {
    const input = activeOnly ? { is_active: true } : {};
    const {
        data: {
            data: { projects: { data = [] } } = { projects: { data: [] } },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            query($input: ProjectSearchInput) {
                projects(input: $input, orderBy: [{ column: NAME, order: ASC }], first: 10000) {
                    data {
                        code: id
                        name
                    }
                }
            }`,
            variables: {
                input: input,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return data;
};
/**
 * Get all project's users
 * Project users are anyone assigned to the project who may or may not be assigned a direct role
 * such as the project manager, customer, or engineer
 * @param projectId<number>
 * @param first<number>
 * @param page<number>
 * @param orderBy<Array<OrderByInterface>>
 * @param filter<ProjectUserFilterInterface>
 */
const getProjectUsers = async function (
    projectId: number,
    first: number = 10,
    page: number = 1,
    orderBy: Array<OrderByInterface> = [{ column: "EMAIL", order: "ASC" }],
    filter: ProjectUserFilterInterface = {},
): Promise<ProjectUserInterface> {
    const input = { id: projectId };
    let filterBy: WhereInterface | ComplexWhereInterface = {};
    let hasRole: WhereInterface | object = {};
    const where: Array<WhereInterface> = [];
    if (!isEmpty(filter?.email)) {
        where.push({
            column: "EMAIL",
            operator: "LIKE",
            value: `%${filter.email}%`,
        });
    }
    if (!isEmpty(filter?.first_name)) {
        where.push({
            column: "FIRST_NAME",
            operator: "LIKE",
            value: `%${filter.first_name}%`,
        });
    }
    if (!isEmpty(filter?.last_name)) {
        where.push({
            column: "LAST_NAME",
            operator: "LIKE",
            value: `%${filter.last_name}%`,
        });
    }
    if (where.length > 1) {
        filterBy = { AND: where };
    } else if (where.length == 1) {
        filterBy = where.pop() ?? {};
    }
    if (!isEmpty(filter?.role)) {
        hasRole = {
            column: "ID",
            operator: "EQ",
            value: filter.role,
        };
    }

    const {
        data: {
            data: {
                project: { users },
            },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `query (
                  $input: ProjectInput!
                  $first: Int!
                  $page: Int!
                  $orderBy: [ProjectUsersOrderByOrderByClause!],
                  $where: ProjectUsersWhereWhereConditions,
                  $hasRoles: ProjectUsersHasRolesWhereHasConditions
                ) {
                  project(input: $input) {
                    users(first: $first, page: $page, orderBy: $orderBy, where: $where, hasRoles: $hasRoles) {
                      data {
                        id
                        first_name
                        last_name
                        email
                        roles {
                          id
                          name
                        }
                      }
                      paginatorInfo {
                        total
                        perPage
                      }
                    }
                  }
                }`,
            variables: {
                input: input,
                first: first,
                page: page,
                orderBy: orderBy,
                where: filterBy,
                hasRoles: hasRole,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return users;
};
/**
 * Update a project
 * @param form<ProjectInterface>
 */
const updateProject = async function (
    form: ProjectWriteInterface,
): Promise<ProjectInterface> {
    const {
        data: { projectUpdate, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation ($input: ProjectUpdateInput!) {
                projectUpdate(input: $input) {
                    id
                }
            }`,
            variables: {
                input: {
                    is_active: form.is_active,
                    name: form.name,
                    id: form.id,
                    description: form.description,
                    customer: { connect: form.customer_id },
                    project_manager: { connect: form.project_manager_id },
                },
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return projectUpdate;
};

export {
    CreateProject,
    DeleteProject,
    GetProject,
    getProjectLog,
    GetProjectNamesOptionList,
    getProjectUsers,
    updateProject,
};
