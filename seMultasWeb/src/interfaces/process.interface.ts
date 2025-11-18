export interface Process {
    id?: number;
    user_id: number;
    client_id: number;
    service_id: number;
    plate: string;
    chassis: string;
    renavam: string;
    state_plate: string;
    infraction_code: string;
    agency: string;
    ait: string;
    seller: string;
    process_value: string;
    payment_method: string;
    observation: string;
    process_number: string;
    deadline_date: string;
    slug?: string;
}