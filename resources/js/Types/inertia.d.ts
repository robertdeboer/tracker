export {};
declare global {
    export namespace inertia {
        export interface Props {
            user: {
                id: number;
                first_name: string;
                last_name: string;
                email: string;
                created_at: Date;
                updated_at: Date;
                roles: [string]
            };
            jetstream: {
                [key: string]: boolean;
            };
            errorBags: unknown;
            errors: unknown;
        }
    }
}
