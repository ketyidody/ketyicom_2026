<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    filters: Object,
    totals: Object,
    topCountries: Array,
    topPages: Array,
    topReferrers: Array,
    viewsPerDay: Array,
    recentVisitors: Array,
});

const range = ref(props.filters.range);

const changeRange = () => {
    router.get(route('admin.statistics.index'), { range: range.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

// Max values for scaling the horizontal bars / column chart.
const maxDaily = computed(() => Math.max(1, ...props.viewsPerDay.map((d) => d.views)));
const maxCountry = computed(() => Math.max(1, ...props.topCountries.map((c) => c.views)));
const maxPage = computed(() => Math.max(1, ...props.topPages.map((p) => p.views)));
const maxReferrer = computed(() => Math.max(1, ...props.topReferrers.map((r) => r.views)));

const expanded = ref(null);
const toggle = (id) => {
    expanded.value = expanded.value === id ? null : id;
};

const formatDate = (value) => new Date(value).toLocaleString();
const formatTime = (value) => new Date(value).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

// Emoji flag from a 2-letter ISO country code.
const flag = (code) => {
    if (!code || code.length !== 2) return '🌐';
    return String.fromCodePoint(
        ...code.toUpperCase().split('').map((c) => 0x1f1e6 + c.charCodeAt(0) - 65)
    );
};

const cards = computed(() => [
    { label: 'Total views', value: props.totals.totalViews },
    { label: 'Unique visitors', value: props.totals.uniqueVisitors },
    { label: 'Today', value: props.totals.viewsToday },
    { label: 'Last 7 days', value: props.totals.views7d },
    { label: 'Last 30 days', value: props.totals.views30d },
]);
</script>

<template>
    <Head title="Statistics" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Website Statistics</h2>
                <select
                    v-model="range"
                    @change="changeRange"
                    class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option :value="7">Last 7 days</option>
                    <option :value="30">Last 30 days</option>
                    <option :value="90">Last 90 days</option>
                    <option :value="365">Last year</option>
                </select>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Stat cards -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div
                        v-for="card in cards"
                        :key="card.label"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5"
                    >
                        <div class="text-3xl font-semibold text-gray-800">{{ card.value.toLocaleString() }}</div>
                        <div class="text-sm text-gray-500 mt-1">{{ card.label }}</div>
                    </div>
                </div>

                <!-- Views per day -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Views per day</h3>
                    <div v-if="viewsPerDay.length" class="flex items-end gap-1 h-40">
                        <div
                            v-for="day in viewsPerDay"
                            :key="day.date"
                            class="flex-1 flex flex-col justify-end items-center group relative"
                        >
                            <div
                                class="w-full bg-indigo-500 hover:bg-indigo-600 rounded-t transition-colors"
                                :style="{ height: (day.views / maxDaily * 100) + '%' }"
                                :class="{ 'min-h-[2px]': day.views === 0 }"
                            ></div>
                            <div
                                class="absolute -top-8 hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap z-10"
                            >
                                {{ day.label }}: {{ day.views }}
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500">No data yet.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top countries -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Top countries</h3>
                        <div v-if="topCountries.length" class="space-y-3">
                            <div v-for="c in topCountries" :key="c.country">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700">{{ flag(c.country_code) }} {{ c.country }}</span>
                                    <span class="text-gray-500">{{ c.views.toLocaleString() }} ({{ c.percent }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div
                                        class="bg-emerald-500 h-2 rounded-full"
                                        :style="{ width: (c.views / maxCountry * 100) + '%' }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-500">No data yet.</p>
                    </div>

                    <!-- Top pages -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Top pages</h3>
                        <div v-if="topPages.length" class="space-y-3">
                            <div v-for="p in topPages" :key="p.url">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700 truncate mr-2">{{ p.url }}</span>
                                    <span class="text-gray-500 flex-shrink-0">{{ p.views.toLocaleString() }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div
                                        class="bg-indigo-500 h-2 rounded-full"
                                        :style="{ width: (p.views / maxPage * 100) + '%' }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-500">No data yet.</p>
                    </div>
                </div>

                <!-- Top referrers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Top referrers</h3>
                    <div v-if="topReferrers.length" class="space-y-3">
                        <div v-for="r in topReferrers" :key="r.referrer">
                            <div class="flex justify-between text-sm mb-1">
                                <a :href="r.referrer" target="_blank" rel="noopener" class="text-indigo-600 hover:underline truncate mr-2">{{ r.referrer }}</a>
                                <span class="text-gray-500 flex-shrink-0">{{ r.views.toLocaleString() }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div
                                    class="bg-amber-500 h-2 rounded-full"
                                    :style="{ width: (r.views / maxReferrer * 100) + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500">No external referrers recorded yet.</p>
                </div>

                <!-- Recent visitor journeys -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Recent visitors</h3>
                    <p class="text-xs text-gray-400 mb-4">Each visitor's movement through the site. Click a row to expand their journey.</p>
                    <div v-if="recentVisitors.length" class="divide-y divide-gray-100">
                        <div v-for="v in recentVisitors" :key="v.session_id" class="py-3">
                            <button
                                type="button"
                                @click="toggle(v.session_id)"
                                class="w-full flex items-center justify-between text-left hover:bg-gray-50 rounded px-2 -mx-2 py-1"
                            >
                                <span class="flex items-center gap-3 text-sm">
                                    <span>{{ flag(v.country_code) }}</span>
                                    <span class="text-gray-700">{{ v.country }}</span>
                                    <span class="text-gray-400 font-mono text-xs">#{{ v.session_id }}</span>
                                </span>
                                <span class="flex items-center gap-3 text-sm text-gray-500">
                                    <span>{{ v.page_count }} page{{ v.page_count === 1 ? '' : 's' }}</span>
                                    <span>{{ formatDate(v.last_seen) }}</span>
                                    <span class="text-gray-400">{{ expanded === v.session_id ? '▲' : '▼' }}</span>
                                </span>
                            </button>
                            <div v-if="expanded === v.session_id" class="mt-3 ml-4 border-l-2 border-gray-200 pl-4 space-y-2">
                                <p v-if="v.referrer" class="text-xs text-gray-400">
                                    Arrived from: <a :href="v.referrer" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">{{ v.referrer }}</a>
                                </p>
                                <div
                                    v-for="(page, i) in v.pages"
                                    :key="i"
                                    class="flex items-center gap-3 text-sm"
                                >
                                    <span class="text-gray-400 text-xs font-mono w-14 flex-shrink-0">{{ formatTime(page.at) }}</span>
                                    <span class="text-gray-300">→</span>
                                    <span class="text-gray-700">{{ page.url }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500">No visitors recorded yet.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
