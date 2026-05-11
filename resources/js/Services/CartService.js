import { reactive } from 'vue';

const CART_KEY = 'webdongho_cart';

export const cart = reactive({
    items: JSON.parse(localStorage.getItem(CART_KEY) || '[]'),
    
    // Notification state
    notification: {
        show: false,
        message: '',
        product: null
    },

    notify(message, product = null) {
        this.notification.message = message;
        this.notification.product = product;
        this.notification.show = true;
        
        // Auto hide after 4 seconds
        setTimeout(() => {
            this.notification.show = false;
        }, 4000);
    },
    
    add(product, quantity = 1) {
        const index = this.items.findIndex(item => item.id === product.id);
        if (index > -1) {
            this.items[index].quantity += quantity;
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                price: product.sale_price || product.price,
                image: product.image,
                quantity: quantity
            });
        }
        this.save();
        this.notify(`Đã thêm "${product.name}" vào giỏ hàng`, product);
    },

    remove(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.save();
    },

    updateQuantity(productId, quantity) {
        const index = this.items.findIndex(item => item.id === productId);
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
