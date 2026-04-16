<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted, nextTick } from 'vue';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { format, differenceInDays } from 'date-fns';
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/dist/css/intlTelInput.css';

const props = defineProps({
    villa: Object,
    checkIn: String,
    checkOut: String,
    guests: [String, Number],
    totalPrice: [String, Number],
    nights: Number,
});

const phoneInput = ref(null);
let iti = null;

onMounted(() => {
    if (phoneInput.value) {
        iti = intlTelInput(phoneInput.value, {
            initialCountry: "auto",
            geoIpLookup: (callback) => {
                fetch("https://ipapi.co/json")
                    .then((res) => res.json())
                    .then((data) => callback(data.country_code))
                    .catch(() => callback("id"));
            },
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
    }
});

const dateRange = ref(props.checkIn && props.checkOut ? [new Date(props.checkIn), new Date(props.checkOut)] : []);
const currentNights = ref(props.nights || 0);
const currentTotalPrice = ref(props.totalPrice || 0);

const form = useForm({
    villa_id: props.villa.id,
    customer_first_name: '',
    customer_last_name: '',
    customer_email: '',
    customer_phone: '',
    check_in: props.checkIn,
    check_out: props.checkOut,
    total_price: props.totalPrice,
    special_requests: '',
});

const handleDateChange = (dates) => {
    if (dates && dates.length === 2) {
        form.check_in = format(dates[0], 'yyyy-MM-dd');
        form.check_out = format(dates[1], 'yyyy-MM-dd');
        
        const nights = differenceInDays(dates[1], dates[0]);
        currentNights.value = nights;
        currentTotalPrice.value = nights * props.villa.price_per_night;
        form.total_price = currentTotalPrice.value;
    } else {
        form.check_in = null;
        form.check_out = null;
        currentNights.value = 0;
        currentTotalPrice.value = 0;
        form.total_price = 0;
    }
};

const submit = () => {
    if (iti) {
        form.customer_phone = iti.getNumber();
    }
    
    if (!form.check_in || !form.check_out) {
        alert('Please select booking dates before proceeding.');
        return;
    }

    form.post(route('booking.store'), {
        onSuccess: (page) => {
            if (page.props.flash.whatsapp_url) {
                window.location.href = page.props.flash.whatsapp_url;
            }
        }
    });
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
    <Head title="Checkout - NGALIH VILLA" />

    <div class="min-h-screen bg-gray-50 pb-24">
        <nav class="bg-teal-900 text-white py-4 px-6 flex justify-between items-center mb-12">
            <Link :href="route('home')" class="text-xl font-bold tracking-tighter uppercase">NGALIH VILLA</Link>
        </nav>

        <div class="max-w-4xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Booking Form -->
                <div>
                    <h2 class="text-2xl font-bold mb-8 uppercase tracking-tight">Personal Details</h2>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">First name *</label>
                                <input v-model="form.customer_first_name" type="text" required class="w-full border-gray-300 rounded-lg p-3 focus:ring-teal-500 focus:border-teal-500" placeholder="e.g. John" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Last name *</label>
                                <input v-model="form.customer_last_name" type="text" required class="w-full border-gray-300 rounded-lg p-3 focus:ring-teal-500 focus:border-teal-500" placeholder="e.g. Doe" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email *</label>
                            <input v-model="form.customer_email" type="email" required class="w-full border-gray-300 rounded-lg p-3 focus:ring-teal-500 focus:border-teal-500" placeholder="john.doe@example.com" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Phone number *</label>
                            <div class="flex flex-col">
                                <input ref="phoneInput" type="tel" required class="w-full border-gray-300 rounded-lg p-3 focus:ring-teal-500 focus:border-teal-500" placeholder="Enter phone number" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Special requests</label>
                            <textarea v-model="form.special_requests" class="w-full border-gray-300 rounded-lg p-3 focus:ring-teal-500 focus:border-teal-500" rows="4" placeholder="e.g. Late check-in, dietary requirements..."></textarea>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-4 rounded-xl transition uppercase tracking-widest mt-8 shadow-lg shadow-teal-900/20"
                        >
                            Complete Booking
                        </button>
                    </form>
                </div>

                <!-- Booking Summary -->
                <div class="bg-white rounded-2xl border p-8 h-fit shadow-xl">
                    <h3 class="text-xl font-bold mb-8 uppercase tracking-tight">Booking Summary</h3>
                    
                    <div class="flex items-center gap-6 mb-8">
                        <img :src="villa.image_url || villa.image" class="w-24 h-24 object-cover rounded-xl shadow-md" />
                        <div>
                            <h4 class="font-bold text-lg text-gray-900">{{ villa.name }}</h4>
                            <p class="text-gray-500 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                {{ guests }} guests
                            </p>
                        </div>
                    </div>

                    <div class="space-y-6 border-t pt-8 mb-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Update Dates</label>
                            <VueDatePicker 
                                v-model="dateRange"
                                range
                                :enable-time-picker="false"
                                placeholder="Check-in — Check-out"
                                @update:model-value="handleDateChange"
                                :min-date="new Date()"
                                input-class-name="modern-datepicker-input"
                            />
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Check-in
                            </span>
                            <span class="font-bold text-gray-900">{{ form.check_in }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Check-out
                            </span>
                            <span class="font-bold text-gray-900">{{ form.check_out }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 border-b pb-6">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Duration
                            </span>
                            <span class="font-bold text-gray-900">{{ currentNights }} nights</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between text-gray-600">
                            <span>{{ formatPrice(villa.price_per_night) }} × {{ currentNights }} nights</span>
                            <span class="font-bold">{{ formatPrice(currentTotalPrice) }}</span>
                        </div>
                        <div class="flex justify-between text-2xl font-bold text-teal-900 pt-4 border-t-2 border-dashed">
                            <span>Total</span>
                            <span>{{ formatPrice(currentTotalPrice) }}</span>
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
                height: 48px !important;
                padding: 0 12px 0 35px !important;
                font-size: 0.875rem !important;
            }
            .dp__theme_light {
                --dp-primary-color: #0f766e;
            }
            .iti {
                width: 100%;
            }
            .iti__country-list {
                z-index: 50;
                border-radius: 8px;
                box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            }
        </style>
