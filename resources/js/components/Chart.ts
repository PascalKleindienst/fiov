// import type { ApexOptions } from 'apexcharts';
import { defineComponent } from '@/utils/component';
import type { ApexOptions } from 'apexcharts';
import ApexCharts from 'apexcharts';

const isDarkMode = () => {
    return (
        localStorage.getItem('flux.appearance') === 'dark' ||
        (!('flux.appearance' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    );
};

function isObject(item: any): boolean {
    return item !== null && typeof item === 'object' && !Array.isArray(item);
}

function deepMerge<T extends object>(target: T, ...sources: Array<Partial<T>>): T {
    if (!sources.length) return target;
    const source = sources.shift();

    if (isObject(target) && isObject(source)) {
        for (const key in source) {
            if (Object.prototype.hasOwnProperty.call(source, key)) {
                const sourceValue = source[key];
                if (isObject(sourceValue) && isObject(target[key])) {
                    target[key] = deepMerge(target[key] as any, sourceValue as any);
                } else {
                    (target as any)[key] = sourceValue;
                }
            }
        }
    }
    return deepMerge(target, ...sources);
}

export default defineComponent((options: ApexOptions) => ({
    chart: null as ApexCharts | null,

    init() {
        const data = options.series ?? [];
        options.series = [];

        const defaultOptions: ApexOptions = {
            chart: {
                type: 'bar',
                height: 350,
                foreColor: isDarkMode() ? '#ccc' : '#373d3f',
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: false
                }
            },
            tooltip: {
                enabled: true,
                theme: isDarkMode() ? 'dark' : 'light',
                x: {
                    show: true
                }
            },
            // dataLabels: {
            //     enabled: true
            //     // background: {
            //     //     enabled: true,
            //     //     foreColor: isDarkMode() ? '#ccc' : '#373d3f'
            //     // }
            // },

            responsive: [
                {
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom',
                            offsetX: -10,
                            offsetY: 0
                        }
                    }
                }
            ],
            plotOptions: {
                bar: {
                    distributed: true,
                    horizontal: false,
                    dataLabels: {
                        total: {
                            enabled: false,
                            style: {
                                color: isDarkMode() ? '#ccc' : '#373d3f',
                                fontSize: '14px',
                                fontWeight: 900
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom'
            },
            fill: {
                opacity: 0.75
            },
            xaxis: {
                labels: {
                    show: true,
                    style: {
                        cssClass: 'text-xs font-normal fill-zinc-500 dark:fill-zinc-300'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                show: true
            }
        };

        if (this.$el.dataset.currency) {
            this.registerCurrencyFormatter(options);
        }

        this.chart = new ApexCharts(this.$el, deepMerge({}, defaultOptions, options));
        this.chart.render();
        this.chart.updateSeries(data);
    },

    registerCurrencyFormatter(options: ApexOptions) {
        const currencyFormatter = new Intl.NumberFormat(undefined, {
            style: 'currency',
            currency: this.$el.dataset.currency
        });

        const formatter = (val: number) => currencyFormatter.format(val);

        if (!options.dataLabels?.formatter) {
            options.dataLabels = options.dataLabels || {};
            options.dataLabels.formatter = formatter;
        }

        if (!options.tooltip?.y?.formatter) {
            options.tooltip = options.tooltip || {};
            options.tooltip.y = options.tooltip.y || {};
            options.tooltip.y.formatter = formatter;
        }

        if (!options.yaxis?.labels?.formatter) {
            options.yaxis = options.yaxis || {};
            options.yaxis.labels = options.yaxis.labels || {};
            options.yaxis.labels.formatter = formatter;
        }
    }
}));
