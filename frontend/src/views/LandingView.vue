<template>
  <div class="bg-[#2c4a5a] min-h-screen flex flex-col items-center">
    <!-- Header -->
    <header class="p-6 flex flex-col items-center bg-[#2c4a5a] rounded-xl m-4 w-full max-w-3xl border border-[#e6e6e6]">
      <img src="@/assets/logo.svg" alt="Moowee Logo" class="w-24 mb-2" />
      <h1 class="text-2xl font-bold text-white">MOOWEE</h1>
      <p class="text-center text-white mt-2 font-semibold">More than a moving company — we're your moving partner</p>
      <p class="text-xs text-white mt-1">Moowee is your trusted partner for stress-free moves.<br>
        We specialize in residential, commercial, and local moving services, delivering reliability, care, and professionalism with every box we lift. Whether you're relocating your home, office, or apartment, Moowee gets you there — smoothly, safely, and on time.
      </p>
    </header>

    <!-- Services Grid -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 m-4 w-full max-w-3xl">
      <div class="bg-[#2c4a5a] rounded-xl p-4 flex flex-col items-center shadow border border-[#e6e6e6]">
        <span class="text-3xl text-white">🏠</span>
        <div class="font-bold mt-2 text-white">RESIDENTIAL MOVING</div>
        <div class="text-xs text-center text-white">Apartment moving<br>House moving</div>
      </div>
      <div class="bg-[#2c4a5a] rounded-xl p-4 flex flex-col items-center shadow border border-[#e6e6e6]">
        <span class="text-3xl text-white">🏢</span>
        <div class="font-bold mt-2 text-white">COMMERCIAL MOVING</div>
        <div class="text-xs text-center text-white">Corporate relocations<br>Office relocations</div>
      </div>
      <div class="bg-[#2c4a5a] rounded-xl p-4 flex flex-col items-center shadow border border-[#e6e6e6]">
        <span class="text-3xl text-white">📦</span>
        <div class="font-bold mt-2 text-white">PACKING SERVICE</div>
        <div class="text-xs text-center text-white">Packing service<br>Packing supplies<br>Unpacking services</div>
      </div>
      <div class="bg-[#2c4a5a] rounded-xl p-4 flex flex-col items-center shadow border border-[#e6e6e6]">
        <span class="text-3xl text-white">📦</span>
        <div class="font-bold mt-2 text-white">STORAGE SOLUTION</div>
        <div class="text-xs text-center text-white">Short-term storage<br>Long-term storage</div>
      </div>
      <div class="bg-[#2c4a5a] rounded-xl p-4 flex flex-col items-center shadow border border-[#e6e6e6]">
        <span class="text-3xl text-white">🎨</span>
        <div class="font-bold mt-2 text-white">SPECIAL MOVINGS</div>
        <div class="text-xs text-center text-white">Piano moving<br>Antique handling<br>Artwork and fragile items</div>
      </div>
      <div class="bg-[#2c4a5a] rounded-xl p-4 flex flex-col items-center shadow border border-[#e6e6e6]">
        <span class="text-3xl text-white">🛠️</span>
        <div class="font-bold mt-2 text-white">LABOR SERVICES</div>
        <div class="text-xs text-center text-white">Loading and unloading services<br>Furniture assembly and disassembly<br>Handyman service</div>
      </div>
      <div class="bg-[#2c4a5a] rounded-xl p-4 flex flex-col items-center shadow border border-[#e6e6e6]">
        <span class="text-3xl text-white">🧹</span>
        <div class="font-bold mt-2 text-white">CLEANING SERVICES</div>
        <div class="text-xs text-center text-white">Pre-move cleaning<br>Post-move cleaning<br>Deep cleaning</div>
      </div>
      <div class="bg-[#2c4a5a] rounded-xl p-4 flex flex-col items-center shadow border border-[#e6e6e6]">
        <span class="text-3xl text-white">⏰</span>
        <div class="font-bold mt-2 text-white">LAST MINUTE SERVICES</div>
        <div class="text-xs text-center text-white">Emergency moving services<br>Same-day moving options</div>
      </div>
      <div class="bg-[#2c4a5a] rounded-xl p-4 flex flex-col items-center shadow border border-[#e6e6e6]">
        <span class="text-3xl text-white">🛡️</span>
        <div class="font-bold mt-2 text-white">INSURANCE</div>
        <div class="text-xs text-center text-white">Basic coverage<br>Full value protection<br>Additional insurance options</div>
      </div>
    </section>

    <!-- Кнопки -->
    <div class="flex justify-center gap-4 m-4 w-full max-w-3xl">
      <button class="btn">GET QUOTE</button>
      <button class="btn bg-green-500 text-white">LEAVE REQUEST</button>
      <button class="btn">PRICES</button>
    </div>

    <!-- Фото -->
    <section class="bg-[#2c4a5a] rounded-xl m-4 p-4 flex flex-col items-center w-full max-w-3xl border border-[#e6e6e6]">
      <h2 class="font-bold text-xl mb-2 text-white">Unloaded photo</h2>
      <img src="https://placehold.co/400x400?text=Photo" alt="Unloaded photo" class="rounded-lg w-full max-w-md" />
    </section>

    <!-- Видео -->
    <section class="bg-[#2c4a5a] rounded-xl m-4 p-4 flex flex-col items-center w-full max-w-3xl border border-[#e6e6e6]">
      <h2 class="font-bold text-xl mb-2 text-white">Unloaded video</h2>
      <video controls class="rounded-lg w-full max-w-md">
        <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4" />
      </video>
    </section>

    <!-- Отзывы -->
    <section class="flex flex-row justify-center gap-2 m-4 w-full max-w-3xl overflow-x-auto">
      <div v-for="i in 5" :key="i" class="bg-[#2c4a5a] rounded-xl p-4 min-w-[200px] shadow border border-[#e6e6e6]">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-yellow-400">★</span>
          <span class="text-yellow-400">★</span>
          <span class="text-yellow-400">★</span>
          <span class="text-yellow-400">★</span>
          <span class="text-yellow-400">★</span>
        </div>
        <div class="text-xs text-white">"Great service! Highly recommend."</div>
        <div class="text-xs mt-2 font-bold text-white">— Customer {{ i }}</div>
      </div>
    </section>

    <!-- Контакты -->
    <footer class="bg-[#2c4a5a] rounded-xl m-4 p-4 text-white w-full max-w-3xl border border-[#e6e6e6]">
      <h2 class="font-bold text-xl">Contacts</h2>
      <div class="flex flex-col gap-1 mt-2">
        <span>+1(310) 753-42-48</span>
        <span>mooweemoving@gmail.com</span>
        <div class="flex gap-2 mt-2">
          <a href="#" class="icon">Instagram</a>
          <a href="#" class="icon">Facebook</a>
          <a href="#" class="icon">YouTube</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
// Можно импортировать реальные фото/видео/иконки, если они появятся
</script>

<style scoped>
.btn {
  @apply px-4 py-2 rounded-lg border border-[#e6e6e6] font-bold shadow bg-[#2c4a5a] text-white;
}
.icon {
  @apply underline;
}
</style> 