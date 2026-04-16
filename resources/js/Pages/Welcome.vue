<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { format, addDays } from 'date-fns';

const props = defineProps({
    villas: Array,
});

const dateRange = ref([]);

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

const submit = (villaId) => {
    const params = { 
        villa: villaId,
        guests: form.guests 
    };
    
    // Logic Correction: Only add dates if they are selected
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
    <Head title="NGALIH VILLA - Luxury Villa Booking" />

    <div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <div class="relative h-[600px] overflow-hidden">
            <img 
                src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                alt="Luxury Villa"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-white">
                <h1 class="text-5xl font-bold mb-4 tracking-tight uppercase">NGALIH VILLA</h1>
                <p class="text-xl mb-8">Search for your perfect stay</p>
                
                <!-- Search Bar -->
                <div class="bg-white p-4 rounded-xl shadow-2xl flex flex-wrap items-end gap-4 text-gray-800 w-full max-w-4xl mx-4">
                    <div class="flex-1 min-w-[300px]">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Check-in — Check-out</label>
                        <VueDatePicker 
                            v-model="dateRange"
                            range
                            multi-calendars
                            :enable-time-picker="false"
                            placeholder="Select Booking Dates"
                            @update:model-value="handleDateChange"
                            :min-date="new Date()"
                            input-class-name="modern-datepicker-input"
                            menu-class-name="modern-datepicker-menu"
                        />
                    </div>
                    <div class="w-32">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Guests</label>
                        <select 
                            v-model="form.guests"
                            class="w-full h-[38px] border-gray-200 rounded-lg focus:ring-teal-500 focus:border-teal-500"
                        >
                            <option v-for="n in 10" :key="n" :value="n">{{ n }} Guests</option>
                        </select>
                    </div>
                    <div>
                        <button class="bg-teal-700 hover:bg-teal-800 text-white px-8 h-[38px] rounded-lg font-semibold transition uppercase text-sm tracking-wider">
                            SEARCH
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Villas -->
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center uppercase tracking-widest">Our Collection</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="villa in villas" :key="villa.id" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition group">
                    <div class="relative h-64 overflow-hidden">
                        <img 
                            :src="villa.image_url || villa.image" 
                            :alt="villa.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        />
                        <div class="absolute top-4 right-4 bg-white/90 px-3 py-1 rounded-full text-sm font-bold text-teal-800">
                            {{ formatPrice(villa.price_per_night) }} / night
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ villa.name }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ villa.description }}</p>
                        
                        <div class="flex items-center gap-4 text-gray-500 text-sm mb-6">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                {{ villa.bedrooms }} BR
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ villa.bathrooms }} BA
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ villa.max_guests }} guests
                            </span>
                        </div>
                        
                        <div class="flex gap-3">
                            <Link 
                                :href="route('villa.show', villa.id)"
                                class="flex-1 text-center py-2 border-2 border-teal-700 text-teal-700 font-semibold rounded-lg hover:bg-teal-50 transition"
                            >
                                VIEW DETAILS
                            </Link>
                            <button 
                                @click="submit(villa.id)"
                                class="flex-1 bg-teal-700 text-white font-semibold rounded-lg hover:bg-teal-800 transition uppercase text-sm"
                            >
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-2xl font-bold mb-4 uppercase">NGALIH VILLA</p>
                <p class="text-gray-400 mb-8">Exclusive villas in the heart of Bali</p>
                <div class="flex justify-center gap-6 mb-8 text-gray-400">
                    <a href="#" class="hover:text-white transition">Instagram</a>
                    <a href="#" class="hover:text-white transition">Facebook</a>
                    <a href="#" class="hover:text-white transition">WhatsApp</a>
                </div>
                <p class="text-gray-600 text-sm">&copy; 2026 NGALIH VILLA. All rights reserved.</p>
            </div>
        </footer>
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
    .modern-datepicker-menu {
        border-radius: 12px !important;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
        border: none !important;
    }
    .dp__theme_light {
        --dp-primary-color: #0f766e;
    }
</style>
