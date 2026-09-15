import { onBeforeUnmount, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";

/**
 * Keeps a search box in sync with the URL, debouncing so a visit is only made
 * once typing pauses. Extra query values (filters, for example) are merged into
 * every visit so a search does not drop the active filter.
 *
 * @param {string} routeName  Inertia route to visit.
 * @param {string} initial    Search term already applied server-side.
 * @param {() => object} extraParams  Additional query params to send.
 * @param {number} delay      Debounce window in milliseconds.
 */
export function useDebouncedSearch(
    routeName,
    initial = "",
    extraParams = () => ({}),
    delay = 300,
) {
    const searchQuery = ref(initial || "");
    let timeout = null;

    const visit = (params) => {
        router.get(route(routeName), params, {
            preserveState: true,
            replace: true,
        });
    };

    watch(searchQuery, (value) => {
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(
            () => visit({ search: value, ...extraParams() }),
            delay,
        );
    });

    // Don't let a queued visit fire after the page has navigated away.
    onBeforeUnmount(() => {
        if (timeout) clearTimeout(timeout);
    });

    const clearSearch = () => {
        if (timeout) clearTimeout(timeout);
        searchQuery.value = "";
        visit({ ...extraParams() });
    };

    return { searchQuery, clearSearch };
}
