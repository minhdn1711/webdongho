import { reactive } from 'vue';

const CART_KEY = 'webdongho_cart';

function makeCartKey(productId, attributes) {
    if (!attributes || Object.keys(attributes).length === 0) return String(productId);
    const sorted = Object.entries(attributes).sort(([a], [b]) => a.localeCompare(b));
    return `${productId}_${sorted.map(([k, v]) => `${k}:${v}`).join('|')}`;
}

export const cart = reactive({
    items: JSON.parse(localStorage.getItem(CART_KEY) || '[]'),

    notification: {
        show: false,
        message: '',
        product: null
    },

    notify(message, product = null) {
        this.notification.message = message;
        this.notification.product = product;
        this.notification.show = true;
        setTimeout(() => { this.notification.show = false; }, 4000);
    },

    add(product, quantity = 1, attributes = null) {
        const cartKey = makeCartKey(product.id, attributes);
        const index = this.items.findIndex(item => item.cartKey === cartKey);

        if (index > -1) {
            this.items[index].quantity += quantity;
        } else {
            this.items.push({
                cartKey,
                id: product.id,
                name: product.name,
                price: product.sale_price || product.price,
                image: product.image,
                quantity,
                attributes: attributes || null,
            });
        }
        this.save();
        this.notify(`Đã thêm "${product.name}" vào giỏ hàng`, product);
    },

    remove(cartKey) {
        this.items = this.items.filter(item => (item.cartKey ?? String(item.id)) !== String(cartKey));
        this.save();
    },

    updateQuantity(cartKey, quantity) {
        const index = this.items.findIndex(item => (item.cartKey ?? String(item.id)) === String(cartKey));
        if (index > -1) {
            this.items[index].quantity = Math.max(1, quantity);
            this.save();
        }
    },

    clear() {
        this.items = [];
        this.save();
    },

    save() {
        localStorage.setItem(CART_KEY, JSON.stringify(this.items));
    },

    get total() {
        return this.items.reduce((sum, item) => sum + item.price * item.quantity, 0);
    },

    get count() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    }
});
