<script setup>
import { useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
//import { loadStripe } from '@stripe/stripe-js';
import { usePage } from '@inertiajs/vue3'


const props = defineProps({
    game: {
        type: Object,
        default: () => null, // Defaults to null for create mode
    },
    players: Array,
    modes: Array,
    routeName: String,
});

const page = usePage();

const goBack = () => {
    window.history.back();
}

//const cardElement = ref(null);
//const stripe = ref(null);
//const stripePublicKey = computed(() => page.props.stripeKey);


const form = useForm({
    name: props.game?.name || '',
    cc_name: page.props.auth.user.first_name+' '+page.props.auth.user.last_name,
    location: props.game?.location || '',
    date_time: props.game?.date_time || '',
    mode_id: props.game?.mode_id || 1,
});

const submit = async () => {
    try {
        // Submit the form
        if(props.routeName == 'games.create') {
            await form.post(route('games.store'), {
                preserveScroll: true, // Prevent scroll reset
            });
        } else {
            await form.put(route('games.update',props.game), {
                preserveScroll: true, // Prevent scroll reset
            });
        }
        // No further action needed; the server will redirect to games.show
    } catch (error) {
        console.error('Error submitting form:', error);
    }

};
/*
onMounted(async () => {
  // Load Stripe.js asynchronously
    stripe.value = await loadStripe(stripePublicKey.value);
    const elements = stripe.value.elements();
    cardElement.value = elements.create('card'); // Assign the created card element to the ref
    cardElement.value.mount('#card-element');
});

onMounted(async () => {
    // Load Stripe.js asynchronously
    stripe.value = await loadStripe(stripePublicKey.value);
    const elements = stripe.value.elements();

    // --- Card Element ---
    cardElement.value = elements.create('card');
    cardElement.value.mount('#card-element');

    // --- Payment Request (Apple Pay / Google Pay) ---
    const paymentRequest = stripe.value.paymentRequest({
        country: 'US',
        currency: 'usd',
        total: {
            label: 'Game Payment',
            amount: 2000, // in cents, update dynamically if needed
        },
        requestPayerName: true,
        requestPayerEmail: true,
    });

    const result = await paymentRequest.canMakePayment();
    if (result) {
        const prButton = elements.create('paymentRequestButton', {
            paymentRequest,
            style: {
                paymentRequestButton: {
                    type: 'default',
                    theme: 'dark',
                    height: '40px',
                },
            },
        });
        prButton.mount('#payment-request-button');

        paymentRequest.on('paymentmethod', async ev => {
            try {
                // Confirm the payment on the server
                const res = await fetch('/payment-intent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ amount: 2000 }), // pass dynamic amount if needed
                });
                const { clientSecret } = await res.json();

                const { error } = await stripe.value.confirmCardPayment(clientSecret, {
                    payment_method: ev.paymentMethod.id,
                });

                if (error) {
                    ev.complete('fail');
                    alert(error.message);
                } else {
                    ev.complete('success');
                    alert('✅ Payment successful!');
                }
            } catch (err) {
                ev.complete('fail');
                alert('❌ Payment failed: ' + err.message);
            }
        });
    }
});
*/
</script>


<template>
    All fields are required
    <div class="mb-10">
        <div v-if="$page.props.errors" class="text-red-800 mb-3 text-lg">
            {{ $page.props.errors.message }}
        </div>


        <form @submit.prevent="submit">
            <div class="flex flex-col sm:flex-row">
                <div class="mr-0 md:mr-3">
                    <InputLabel for="name" value="Game Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full sm:w-96"
                        required
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mr-0 md:mr-3">
                    <InputLabel for="location" value="Location" />
                    <TextInput
                        id="location"
                        v-model="form.location"
                        type="text"
                        class="mt-1 block w-full sm:w-96"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.location" />
                </div>
            </div>
            <!-- CC PROCESSING
            <div class="flex flex-col sm:flex-row mt-3" v-if="routeName == 'games.create' ">
                <div class="mr-0 md:mr-3">
                    <InputLabel for="cc_name" value="Name" />
                    <TextInput
                        id="location"
                        v-model="form.cc_name"
                        type="text"
                        class="mt-1 block w-full sm:w-96"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.cc_name" />
                </div>
                <div class="flex-auto mr-2" >
                    <InputLabel for="card-element" value="Card Number" />
                    <div id="card-element"></div>
                    <InputError class="mt-2" />
                </div>
                <div class="mt-3">
                    <div id="payment-request-button"></div>
                    <p class="text-sm text-gray-500 mt-2">Or pay with your card:</p>
                </div>
            </div>
            END CC PROCESSING
            -->
            <div class="flex flex-col sm:flex-row">
                <div class="flex items-center mt-10">

                    <SecondaryButton type="button" class="mr-3" @click="goBack">
                        <span>Back</span>
                    </SecondaryButton>
                    <PrimaryButton  :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ routeName == 'games.create' ? 'Continue' : 'Update Game'  }}
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </div>
</template>

<style>
#card-element {
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-top-left-radius: 6px;
  border-top-right-radius: 6px;
  border-bottom-left-radius: 6px;
  border-bottom-right-radius: 6px;
  padding: 12px;
}

</style>
