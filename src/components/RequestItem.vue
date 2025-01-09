<template>
  <div class="w-full max-w-[380px] p-4 bg-white rounded-lg shadow-md flex flex-col gap-4">
    <!-- Header Section -->
    <div class="w-full flex items-center justify-between bg-neutral-100 rounded-lg p-2">
      <div class="text-gray-900 text-base font-medium font-montserrat">Request number</div>
      <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-1">
        <div class="opacity-60 text-sm text-gray-900 font-semibold font-montserrat">Price</div>
        <div class="text-gray-900 text-base font-normal font-montserrat">12$</div>
      </div>
    </div>

    <!-- Type and Time Section -->
    <div class="w-full flex justify-between gap-4">
      <div class="flex flex-col gap-1">
        <div class="text-sm text-gray-900 opacity-60 font-semibold font-montserrat">Type</div>
        <div class="text-gray-900 text-base font-normal font-montserrat">3 bedroom</div>
      </div>
      <div class="flex flex-col gap-1">
        <div class="text-sm text-gray-900 opacity-60 font-semibold font-montserrat">Time</div>
        <div class="text-gray-900 text-base font-normal font-montserrat">12/11/24 | 12:00</div>
      </div>
    </div>

    <!-- Loading Address Section -->
    <div class="w-full flex flex-col gap-1">
      <div class="text-sm text-gray-900 opacity-60 font-semibold font-montserrat">Loading address</div>
      <div class="text-gray-900 text-base font-normal font-montserrat">Here goes address of loading</div>
    </div>

    <!-- Unloading Address Section -->
    <div class="w-full flex flex-col gap-1">
      <div class="text-sm text-gray-900 opacity-60 font-semibold font-montserrat">Unloading address</div>
      <div class="text-gray-900 text-base font-normal font-montserrat">Here goes address of unloading</div>
    </div>
  </div>
</template>

<style scoped>

</style>
