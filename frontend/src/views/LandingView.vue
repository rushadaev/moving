<template>
  <div class="min-h-screen bg-white w-full">
    <!-- Navigation - Black style -->
    <nav class="fixed top-0 w-full bg-black/95 backdrop-blur-xl z-50 border-b border-white/10 shadow-lg">
      <div class="w-full px-8 lg:px-16">
        <div class="flex items-center justify-between h-16">
          <div class="text-2xl font-bold tracking-tight text-white">moowwee</div>
          <div class="hidden md:flex items-center gap-10 text-sm font-medium text-white">
            <a href="#residential" class="hover:text-blue-400 transition-colors">Residential</a>
            <a href="#commercial" class="hover:text-blue-400 transition-colors">Commercial</a>
            <a href="#storage" class="hover:text-blue-400 transition-colors">Storage</a>
            <a href="#support" class="hover:text-blue-400 transition-colors">Support</a>
            <button @click="handleGetQuote" class="bg-white hover:bg-gray-200 text-black px-6 py-2 rounded-full transition-colors font-semibold">
              Get Quote
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section with Background Image -->
    <section class="relative w-full h-screen">
      <!-- Background Image -->
      <div class="absolute inset-0 w-full h-full">
        <img
          src="https://images.unsplash.com/photo-1574227136494-46cf4c368a4e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtaW5pbWFsaXN0JTIwd2hpdGUlMjByb29tJTIwYnJpZ2h0fGVufDF8fHx8MTc3MDg3MTk4Mnww&ixlib=rb-4.1.0&q=80&w=1920"
          alt="Modern interior"
          class="w-full h-full object-cover"
        />
        <!-- Dark overlay for better text readability -->
        <div class="absolute inset-0 bg-black/30"></div>
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 h-full flex flex-col items-center justify-center px-8 lg:px-16 text-center">
        <h1 class="text-6xl md:text-7xl lg:text-8xl font-bold tracking-tight mb-6 text-white drop-shadow-2xl">
          moowwee
        </h1>
        <p class="text-3xl md:text-4xl text-white mb-10 drop-shadow-lg">
          Moving reimagined.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center text-lg text-white">
          <span class="text-2xl font-semibold">From $299</span>
          <button
            @click="handleGetQuote"
            class="bg-white hover:bg-gray-200 text-black px-8 py-3 rounded-full transition-colors font-semibold text-lg flex items-center gap-2"
          >
            Get a quote
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
      </div>
    </section>

    <!-- Feature Callout 1 -->
    <section class="py-24 bg-black text-white w-full">
      <div class="w-full px-8 lg:px-16 text-center">
        <h2 class="text-4xl md:text-6xl font-semibold mb-4 tracking-tight">
          Seamless. From start to finish.
        </h2>
        <p class="text-xl md:text-2xl text-gray-400">
          Experience moving the way it should be.
        </p>
      </div>
    </section>

    <!-- Feature Section - Residential -->
    <section id="residential" class="py-24 w-full">
      <div class="w-full px-8 lg:px-16">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <div>
            <div class="text-sm font-semibold text-orange-600 mb-2">RESIDENTIAL</div>
            <h2 class="text-4xl md:text-5xl font-semibold mb-6 tracking-tight text-black">
              Your home.<br />Our priority.
            </h2>
            <p class="text-xl text-black leading-relaxed mb-8">
              From packing your first box to placing the last piece of furniture,
              our team handles every detail with care. Because your belongings aren't just things—they're your life.
            </p>
            <div class="space-y-3 text-lg text-black">
              <div class="flex items-center gap-3">
                <div class="w-1 h-1 bg-black rounded-full"></div>
                <span>Full-service packing</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-1 h-1 bg-black rounded-full"></div>
                <span>Climate-controlled transport</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-1 h-1 bg-black rounded-full"></div>
                <span>White-glove service</span>
              </div>
            </div>
          </div>
          <div class="aspect-square rounded-2xl overflow-hidden">
            <img
              src="https://images.unsplash.com/photo-1606932250069-62f395a08602?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxoYXBweSUyMGZhbWlseSUyMG5ldyUyMGhvbWV8ZW58MXx8fHwxNzcwODcxOTgzfDA&ixlib=rb-4.1.0&q=80&w=1080"
              alt="Happy family"
              class="w-full h-full object-cover"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Feature Section - Commercial -->
    <section id="commercial" class="py-24 bg-gray-50 w-full">
      <div class="w-full px-8 lg:px-16">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <div class="order-2 md:order-1 aspect-square rounded-2xl overflow-hidden">
            <img
              src="https://images.unsplash.com/photo-1718066236079-9085195c389e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBhcGFydG1lbnQlMjBlbXB0eSUyMGJyaWdodHxlbnwxfHx8fDE3NzA4NzE5ODN8MA&ixlib=rb-4.1.0&q=80&w=1080"
              alt="Modern office"
              class="w-full h-full object-cover"
            />
          </div>
          <div class="order-1 md:order-2">
            <div class="text-sm font-semibold text-blue-600 mb-2">COMMERCIAL</div>
            <h2 class="text-4xl md:text-5xl font-semibold mb-6 tracking-tight text-black">
              Business moves.<br />Zero downtime.
            </h2>
            <p class="text-xl text-black leading-relaxed mb-8">
              We coordinate every aspect of your office relocation. Your team can focus on what matters
              while we handle the logistics.
            </p>
            <div class="space-y-3 text-lg text-black">
              <div class="flex items-center gap-3">
                <div class="w-1 h-1 bg-black rounded-full"></div>
                <span>After-hours moving</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-1 h-1 bg-black rounded-full"></div>
                <span>IT equipment specialists</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-1 h-1 bg-black rounded-full"></div>
                <span>Project management</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="py-24 w-full">
      <div class="w-full px-8 lg:px-16">
        <div class="grid grid-cols-3 gap-8 text-center">
          <div>
            <div class="text-5xl md:text-6xl font-semibold mb-2 text-black">10K+</div>
            <div class="text-black">Moves completed</div>
          </div>
          <div>
            <div class="text-5xl md:text-6xl font-semibold mb-2 text-black">4.9</div>
            <div class="text-black">Customer rating</div>
          </div>
          <div>
            <div class="text-5xl md:text-6xl font-semibold mb-2 text-black">100%</div>
            <div class="text-black">Insured</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Storage Section -->
    <section id="storage" class="py-24 bg-gray-50 w-full">
      <div class="w-full px-8 lg:px-16 text-center">
        <div class="text-sm font-semibold text-purple-600 mb-2">STORAGE</div>
        <h2 class="text-4xl md:text-5xl font-semibold mb-6 tracking-tight text-black">
          Keep your things safe.<br />For as long as you need.
        </h2>
        <p class="text-xl text-black max-w-2xl mx-auto mb-12 leading-relaxed">
          Climate-controlled facilities. 24/7 security. Flexible terms.
          Your belongings are in good hands.
        </p>
        <div class="grid md:grid-cols-3 gap-8 max-w-3xl mx-auto">
          <div>
            <div class="text-3xl font-semibold mb-2 text-black">Climate</div>
            <div class="text-black">controlled</div>
          </div>
          <div>
            <div class="text-3xl font-semibold mb-2 text-black">24/7</div>
            <div class="text-black">Security</div>
          </div>
          <div>
            <div class="text-3xl font-semibold mb-2 text-black">Flexible</div>
            <div class="text-black">Terms</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-32 w-full bg-gradient-to-b from-white to-gray-50">
      <div class="w-full px-8 lg:px-16 text-center">
        <h2 class="text-4xl md:text-5xl font-semibold mb-6 tracking-tight text-black">
          Ready to get moving?
        </h2>
        <p class="text-xl text-black mb-8">
          Get a free quote in 60 seconds.
        </p>
        <button
          @click="handleGetQuote"
          class="bg-blue-600 hover:bg-blue-700 h-12 px-6 rounded-full text-base text-white font-medium transition-colors"
        >
          Get your free quote
        </button>
        <div class="mt-4 text-sm text-black">
          Or call us at +1 (310) 753-42-48
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer id="support" class="bg-gray-900 text-gray-300 py-16 border-t border-gray-800 w-full">
      <div class="w-full px-8 lg:px-16">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8 mb-8">
          <div>
            <div class="font-semibold mb-3 text-sm text-gray-400 uppercase tracking-wider">Services</div>
            <ul class="space-y-2 text-sm">
              <li><a href="#residential" class="hover:text-white transition-colors">Residential</a></li>
              <li><a href="#commercial" class="hover:text-white transition-colors">Commercial</a></li>
              <li><a href="#storage" class="hover:text-white transition-colors">Storage</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Packing</a></li>
            </ul>
          </div>
          <div>
            <div class="font-semibold mb-3 text-sm text-gray-400 uppercase tracking-wider">Company</div>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-white transition-colors">About</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
            </ul>
          </div>
          <div>
            <div class="font-semibold mb-3 text-sm text-gray-400 uppercase tracking-wider">Support</div>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-white transition-colors">FAQ</a></li>
              <li><a href="#" @click.prevent="handleGetQuote" class="hover:text-white transition-colors cursor-pointer">Get a Quote</a></li>
              <li><router-link to="/tracking" class="hover:text-white transition-colors">Track Move</router-link></li>
            </ul>
          </div>
          <div>
            <div class="font-semibold mb-3 text-sm text-gray-400 uppercase tracking-wider">Legal</div>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-white transition-colors">Privacy</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Terms</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Insurance</a></li>
            </ul>
          </div>
          <div>
            <div class="font-semibold mb-3 text-sm text-gray-400 uppercase tracking-wider">Contact</div>
            <ul class="space-y-2 text-sm">
              <li class="hover:text-white transition-colors">mooweemoving@gmail.com</li>
              <li class="hover:text-white transition-colors">+1 (310) 753-42-48</li>
            </ul>
          </div>
        </div>
        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
          <div class="text-gray-400">Copyright © 2026 moowwee Inc. All rights reserved.</div>
          <div class="flex gap-8">
            <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-white transition-colors">Terms of Use</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

const handleGetQuote = () => {
  // Navigate to requests page or open modal
  router.push('/requests')
}
</script>

<style scoped>
/* Any additional styles if needed */
</style>
