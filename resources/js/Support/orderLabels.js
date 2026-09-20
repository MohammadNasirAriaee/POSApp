/**
 * Display names for the two optional relations on an order. Both fall back to
 * a placeholder when the order has no linked customer or employee (a walk-in
 * sale, or one entered before staff accounts existed).
 */
export const customerName = (order) => order.customer?.name || "Walk-in Customer";

export const cashierName = (order) => order.employee?.name || "Admin";
