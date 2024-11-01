export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User;
    };
};

export interface Invoice {
    id: number;
    number: number;
    customer: string;
    total: number;
}

export interface InvoiceProduct {
    name: string;
    price: number;
    quantity: number;
}
