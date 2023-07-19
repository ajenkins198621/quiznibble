export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};

type DefaultItems = {
    id: number;
    created_at: string;
    updated_at: string;
}

export type EditCategoryProp = DefaultItems & {
    category_name: string;
    parent_id: null | number;
    tags?: EditTagProp[]
}

export type EditTagProp = DefaultItems & {
    tag_name: string;
    parent_id: null | number;
}
