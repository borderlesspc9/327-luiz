export interface RequestParams {
    search?: string;
    per_page?: number;
    page?: number;
    order_by?: string;
    order_direction?: string;
    limit?: number;
    without_pagination?: number;
    filter?: {
        deadline_date?: string | null;
        status?: string | null
    };
}