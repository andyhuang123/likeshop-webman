<script lang="ts">
import { ElIcon } from 'element-plus'
import { createVNode, defineComponent, h, resolveComponent } from 'vue'

import { EL_ICON_PREFIX, LOCAL_ICON_PREFIX } from './index'
import svgIcon from './svg-icon.vue'

export default defineComponent({
    name: 'Icon',
    props: {
        name: {
            type: String,
            required: true
        },
        size: {
            type: [String, Number],
            default: '14px'
        },
        color: {
            type: String,
            default: 'inherit'
        },
        label: {
            type: String,
            default: ''
        }
    },
    setup(props) {
        return () => {
            // 创建图标内容
            let iconContent
            if (props.name.indexOf(EL_ICON_PREFIX) === 0) {
                // el-icon
                iconContent = createVNode(
                    ElIcon,
                    {
                        size: props.size,
                        color: props.color
                    },
                    {
                        default: () =>
                            createVNode(resolveComponent(props.name.replace(EL_ICON_PREFIX, '')))
                    }
                )
            } else if (props.name.indexOf(LOCAL_ICON_PREFIX) === 0) {
                // 本地icon
                iconContent = h(
                    'i',
                    {
                        class: ['local-icon']
                    },
                    createVNode(svgIcon, { ...props })
                )
            } else {
                // 如果name不符合预期的前缀，返回null
                return null
            }

            // 如果传入了 label，则包装一层 span 并添加 aria-label
            if (props.label) {
                return h(
                    'span',
                    {
                        class: 'icon-wrapper',
                        role: 'img',
                        'aria-label': props.label
                    },
                    iconContent
                )
            }

            return iconContent
        }
    }
})
</script>

<style scoped>
.icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>
