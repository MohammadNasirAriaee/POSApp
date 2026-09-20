/**
 * The payment methods StoreOrderRequest accepts (cash|card|bank_transfer),
 * with the label shown wherever a method needs to be picked or displayed.
 */
export const PAYMENT_METHODS = [
    { value: "cash", label: "Cash" },
    { value: "card", label: "Card" },
    { value: "bank_transfer", label: "Bank Transfer" },
];

const LABELS_BY_VALUE = Object.fromEntries(
    PAYMENT_METHODS.map(({ value, label }) => [value, label]),
);

export const paymentMethodLabel = (value) => LABELS_BY_VALUE[value] ?? value;
