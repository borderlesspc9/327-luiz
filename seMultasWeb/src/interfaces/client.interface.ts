import type { Process } from "./process.interface";

export interface Client {
    id?: number;
    name: string;
    nuvem: string;
    phone: string;
    cpf: string;
    profession: string;
    rg: string;
    state: string;
    city: string;
    address: string;
    number: string;
    complement: string;
    process?: Process[];
    birth_date?: string | null;
    marital_status?: string | null;
    created_at?: string;
    license_date?: string | null;
    neighborhood?: string | null;
    slug?: string;
    updated_at?: string;
}

export interface ClientData {
    data: Client[];
}