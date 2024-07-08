import TailwindcssAutocomplete from 'https://esm.sh/@yabe-siul/tailwindcss-autocomplete/dist/index.js';

let autocomplete;

function initializeAutocomplete(config) {
    if (config) {
        autocomplete = new TailwindcssAutocomplete(config);
        console.log('Tailwind Autocomplete initialized with config:', config);
        window.autocomplete = autocomplete;
    }
}

function updateAutocompleteConfig(config) {
    if (config && autocomplete) {
        autocomplete.config = config;
        console.log('Tailwind Autocomplete config updated:', config);
    }
}

window.getSuggestionList = async function(value) {
    if (!autocomplete) {
        console.error('Autocomplete is not initialized.');
        return [];
    }
    console.time('Fetching suggestions');
    try {
        const list = await autocomplete.getSuggestionList(value);
        console.timeEnd('Fetching suggestions');

        return list.map(item => {
            return item;
    });
    } catch (error) {
        console.error('Error fetching suggestions:', error);
        return [];
    }
};

// Global function to update the config
window.tailwindUpdateConfig = function(newConfig) {
    if (!autocomplete) {
        initializeAutocomplete(newConfig);
    } else {
        updateAutocompleteConfig(newConfig);
    }
};

// Set up a custom setter for window.tailwind.config
let tailwindConfig = window.tailwind?.config;
Object.defineProperty(window, 'tailwind', {
    get() {
        return { config: tailwindConfig };
    },
    set(newValue) {
        if (newValue && newValue.config !== tailwindConfig) {
            tailwindConfig = newValue.config;
            window.tailwindUpdateConfig(tailwindConfig);
        }
    }
});

// Initialize if config is already available
if (tailwindConfig) {
    initializeAutocomplete(tailwindConfig);
} else {
    // Check periodically for config
    const checkConfigInterval = setInterval(() => {
        if (window.tailwind?.config) {
            tailwindConfig = window.tailwind.config;
            initializeAutocomplete(tailwindConfig);
            clearInterval(checkConfigInterval);
        }
    }, 1000);
}


(function(window) {
    const moduleClassSorter = {
        sort(classes) {
            let tailwindConfig = window.tailwind.config;
            const twContext = window.twContext; // Assuming twContext is globally available

            const parts = classes
                .split(/\s+/)
                .filter((x) => x !== "" && x !== "|");

            const unknownClassNames = [];

            const knownClassNamesWithOrder = twContext.getClassOrder
                ? twContext.getClassOrder(parts)
                : this.getClassOrderPolyfill(parts, twContext);

            const knownClassNames = knownClassNamesWithOrder
                .sort(([, a], [, z]) => {
                    if (a === z) return 0;
                    if (a === null) return -1;
                    if (z === null) return 1;
                    return this.bigSign(a - z);
                })
                .map(([className]) => className);

            return [...unknownClassNames, ...knownClassNames].join(" ");
        },

        bigSign(bigIntValue) {
            return (bigIntValue > 0n) - (bigIntValue < 0n);
        },

        prefixCandidate(context, selector) {
            const prefix = context.tailwindConfig.prefix;
            return typeof prefix === "function" ? prefix(selector) : prefix + selector;
        },

        getClassOrderPolyfill(classes, context) {
            const parasiteUtilities = new Set([
                this.prefixCandidate(context, "group"),
                this.prefixCandidate(context, "peer"),
            ]);

            const classNamesWithOrder = [];

            for (const className of classes) {
                let order =
                    window.generateRules.generateRules(new Set([className]), context).sort(([a], [z]) =>
                        this.bigSign(z - a)
                    )[0]?.[0] ?? null;

                if (order === null && parasiteUtilities.has(className)) {
                    order = context.layerOrder.components;
                }

                classNamesWithOrder.push([className, order]);
            }

            return classNamesWithOrder;
        },
    };

    // Make the moduleClassSorter available globally
    window.TailwindClassSorter = moduleClassSorter;

})(window);