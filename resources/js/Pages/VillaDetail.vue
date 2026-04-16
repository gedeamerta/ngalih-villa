<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { format } from 'date-fns';

const props = defineProps({
    villa: Object,
    bookedDates: Array,
});

const dateRange = ref([]);

const disabledDates = computed(() => {
    return props.bookedDates.map(date => new Date(date));
});

const form = useForm({
    check_in: '',
    check_out: '',
    guests: 1,
});

const handleDateChange = (dates) => {
    if (dates && dates.length === 2) {
        form.check_in = dates[0] ? format(dates[0], 'yyyy-MM-dd') : '';
        form.check_out = dates[1] ? format(dates[1], 'yyyy-MM-dd') : '';
    } else {
        form.check_in = '';
        form.check_out = '';
    }
};

const submit = () => {
    const params = { 
        villa: props.villa.id,
        guests: form.guests 
    };
    
    if (form.check_in && form.check_out) {
        params.check_in = form.check_in;
        params.check_out = form.check_out;
    } else {
        params.check_in = null;
        params.check_out = null;
    }
    
    form.get(route('villa.checkout', params));
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(price);
};
</script>

<template>
    <Head :title="villa.name + ' - NGALIH VILLA'" />

    <div class="min-h-screen bg-white">
        <!-- Navigation -->
        <nav class="bg-teal-900 text-white py-4 px-6 flex justify-between items-center sticky top-0 z-50">
            <Link :href="route('home')" class="text-xl font-bold tracking-tighter uppercase">NGALIH VILLA</Link>
            <div class="flex gap-8 text-sm font-semibold">
                <Link :href="route('home')" class="hover:text-teal-200 uppercase">Home</Link>
                <a href="#" class="hover:text-teal-200 uppercase">Collection</a>
            </div>
            <button class="bg-teal-600 hover:bg-teal-700 px-6 py-2 rounded text-xs font-bold uppercase tracking-widest transition">Book Now</button>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Left Content -->
                <div class="lg:col-span-2">
                    <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ villa.name }}</h1>
                    
                    <!-- Quick Info -->
                    <div class="flex flex-wrap gap-8 text-sm text-gray-600 mb-8 border-b pb-8">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            {{ villa.bedrooms }} bedrooms
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                            {{ villa.bathrooms }} bathrooms
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            {{ villa.area_sqm }} m²
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Up to {{ villa.max_guests }} guests
                        </span>
                    </div>

                    <div class="prose prose-teal max-w-none text-gray-600 mb-12">
                        <p>{{ villa.description }}</p>
                    </div>

                    <!-- Amenities -->
                    <div class="mb-12">
                        <h3 class="text-xl font-bold mb-6 text-gray-900 uppercase tracking-widest text-sm">Services Included</h3>
                        <div class="grid grid-cols-2 gap-y-4">
                            <div v-for="amenity in villa.amenities" :key="amenity" class="flex items-center gap-3 text-gray-600">
                                <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                                {{ amenity }}
                            </div>
                        </div>
                    </div>

                    <img :src="villa.image_url || villa.image" class="w-full h-[500px] object-cover rounded-2xl mb-12" />
                </div>

                <!-- Right Sidebar (Booking Widget) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border p-8 shadow-xl sticky top-24">
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-center mb-6 uppercase tracking-wider">Select Dates</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Booking Dates</label>
                                    <VueDatePicker 
                                        v-model="dateRange"
                                        range
                                        :enable-time-picker="false"
                                        placeholder="Check-in — Check-out"
                                        @update:model-value="handleDateChange"
                                        :min-date="new Date()"
                                        :disabled-dates="disabledDates"
                                        input-class-name="modern-datepicker-input"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Guests</label>
                                    <select v-model="form.guests" class="w-full h-[38px] border-gray-200 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm">
                                        <option v-for="n in villa.max_guests" :key="n" :value="n">{{ n }} Guests</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 space-y-4">
                            <div class="flex justify-between text-gray-600">
                                <span>{{ formatPrice(villa.price_per_night) }} × nights</span>
                                <span class="font-semibold">—</span>
                            </div>
                            <div class="flex justify-between text-xl font-bold text-teal-900 pt-2 border-t">
                                <span>Total</span>
                                <span>—</span>
                            </div>
                            <button 
                                @click="submit"
                                class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-4 rounded-xl transition uppercase tracking-widest mt-4"
                            >
                                Continue
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .modern-datepicker-input {
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        height: 38px !important;
        padding: 0 12px 0 35px !important;
        font-size: 0.875rem !important;
    }
    .dp__theme_light {
        --dp-primary-color: #0f766e;
    }
</style>
