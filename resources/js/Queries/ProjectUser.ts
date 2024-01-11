import axios from "axios";
import { ProjectInterface } from "@/Types/Project";
import { UserInterface } from "@/Types/User";
/**
 * Assigned an user to a project
 * @param projectId<number>
 * @param userId<number>
 */
const assignProjectUser = async function (
    projectId: number,
    userId: number,
): Promise<ProjectInterface> {
    const {
        data: { projectUpdate, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($project: ID!, $user: ID!) {
                projectUpdate(input: { id: $project, users: { connect: [$user] } }) {
                    id
                }
            }`,
            variables: {
                project: projectId,
                user: userId,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return projectUpdate;
};
/**
 * Get all users not assigned to the project
 * @param projectId<number>
 */
const getNonProjectUsers = async function (
    projectId: number,
): Promise<Array<UserInterface>> {
    const {
        data: {
            data: { nonProjectUser },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `query($input: nonProjectUsersInput!) {
                nonProjectUser(input: $input) {
                    id
                    first_name
                    last_name
                    roles {
                        name
                    }
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
    return nonProjectUser;
};

/**
 * Remove an assigned user from a project
 * @param projectId<number>
 * @param userId<number>
 */
const removeProjectUser = async function (
    projectId: number,
    userId: number,
): Promise<ProjectInterface> {
    const {
        data: { projectUpdate, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($project: ID!, $user: ID!) {
                projectUpdate(input: { id: $project, users: { disconnect: [$user] } }) {
                    id
                }
            }`,
            variables: {
                project: projectId,
                user: userId,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return projectUpdate;
};

export { assignProjectUser, getNonProjectUsers, removeProjectUser };
