<script setup>
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Nhập nội dung...',
    },
    height: {
        type: String,
        default: '300px',
    },
});

const emit = defineEmits(['update:modelValue']);

const toolbarOptions = [
    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ 'color': [] }, { 'background': [] }],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
    [{ 'align': [] }],
    ['blockquote', 'code-block'],
    ['link', 'image'],
    ['clean'],
];

const onUpdate = (content) => {
    emit('update:modelValue', content);
};
</script>

<template>
    <div class="rich-editor">
        <QuillEditor
            :content="modelValue"
            content-type="html"
            :toolbar="toolbarOptions"
            :placeholder="placeholder"
            theme="snow"
            @update:content="onUpdate"
        />
    </div>
</template>

<style scoped>
.rich-editor :deep(.ql-toolbar) {
    border-color: #c3c4c7 !important;
    background: #f0f0f1;
    border-radius: 0;
}

.rich-editor :deep(.ql-container) {
    border-color: #c3c4c7 !important;
    font-size: 14px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    min-height: v-bind(height);
}

.rich-editor :deep(.ql-editor) {
    min-height: v-bind(height);
    line-height: 1.6;
    color: #2c3338;
}

.rich-editor :deep(.ql-editor.ql-blank::before) {
    color: #a7aaad;
    font-style: normal;
}

.rich-editor :deep(.ql-snow .ql-picker) {
    color: #1d2327;
}

.rich-editor :deep(.ql-snow .ql-stroke) {
    stroke: #50575e;
}

.rich-editor :deep(.ql-snow .ql-fill) {
    fill: #50575e;
}

.rich-editor :deep(.ql-snow button:hover .ql-stroke),
.rich-editor :deep(.ql-snow .ql-picker-label:hover .ql-stroke) {
    stroke: #2271b1;
}

.rich-editor :deep(.ql-snow button:hover .ql-fill),
.rich-editor :deep(.ql-snow .ql-picker-label:hover .ql-fill) {
    fill: #2271b1;
}

.rich-editor :deep(.ql-snow button.ql-active .ql-stroke) {
    stroke: #2271b1;
}

.rich-editor :deep(.ql-snow button.ql-active .ql-fill) {
    fill: #2271b1;
}
</style>
