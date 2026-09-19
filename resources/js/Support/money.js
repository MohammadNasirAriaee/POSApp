/**
 * Formats a monetary amount for display.
 *
 * Values arrive from the API as decimal strings ("12.50"), so they are coerced
 * before formatting. Anything unparseable renders as zero rather than "$NaN".
 */
export const formatMoney = (amount) =>
    new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(Number(amount) || 0);
