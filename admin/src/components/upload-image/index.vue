<template>
    <material-picker
        v-model="value"
        :limit="limit"
        :disabled="disabled"
        :size="size"
        type="image"
        upload-class="bg-body"
        exclude-domain
    />
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import MaterialPicker from '@/components/material/picker.vue'

const props = defineProps({
    modelValue: {
        type: [String, Array],
        default: ''
    },
    limit: {
        type: Number,
        default: 1
    },
    disabled: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: '100px'
    }
})

const emit = defineEmits(['update:modelValue'])

const value = computed({
    get() {
        return props.modelValue
    },
    set(val) {
        emit('update:modelValue', val)
    }
})
</script>
