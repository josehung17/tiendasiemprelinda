import './bootstrap';

document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        items: [],
        
        init() {
            // Persistence can be added here later
        },

        add(product) {
            if (!product) return;

            const existingItem = this.items.find(item => item.id === product.id && item.zone_display_id === product.zone_display_id);

            if (existingItem) {
                if (existingItem.quantity < existingItem.stock_display) {
                    existingItem.quantity++;
                    Livewire.dispatch('app-notification-success', { message: `Cantidad de ${product.nombre} actualizada` });
                } else {
                    Livewire.dispatch('app-notification-error', { message: `Stock máximo alcanzado para ${product.nombre}` });
                }
            } else {
                if (product.stock_display > 0) {
                    let newItem = { ...product, quantity: 1 };
                    this.items.push(newItem);
                    Livewire.dispatch('app-notification-success', { message: `${product.nombre} agregado` });
                } else {
                    Livewire.dispatch('app-notification-error', { message: `No hay stock para ${product.nombre}` });
                }
            }
        },

        remove(productId, zoneId) {
            const itemName = this.items.find(item => item.id === productId && item.zone_display_id === zoneId)?.nombre || 'Producto';
            this.items = this.items.filter(item => !(item.id === productId && item.zone_display_id === zoneId));
            Livewire.dispatch('app-notification-info', { message: `${itemName} eliminado` });
        },

        updateQuantity(productId, zoneId, newQuantity) {
            const item = this.items.find(i => i.id === productId && i.zone_display_id === zoneId);
            if (!item) return;

            const qty = parseInt(newQuantity);

            if (isNaN(qty) || qty <= 0) {
                this.remove(productId, zoneId);
                return;
            }

            if (qty > item.stock_display) {
                item.quantity = item.stock_display;
                Livewire.dispatch('app-notification-error', { message: `Stock máximo: ${item.stock_display}` });
            } else {
                item.quantity = qty;
            }
            // Force reactivity
            this.items = [...this.items];
        },

        actualizarZonaItem(productId, oldZoneId, newZoneId, newZoneName, newStockDisponible) {
            const itemIndex = this.items.findIndex(item => item.id === productId && item.zone_id === oldZoneId);

            if (itemIndex !== -1) {
                // Check if an item with the new zone already exists
                const existingItemWithNewZone = this.items.find(item => item.id === productId && item.zone_id === newZoneId);

                if (existingItemWithNewZone) {
                    // If it exists, merge quantities and remove the old item
                    existingItemWithNewZone.quantity += this.items[itemIndex].quantity;
                    existingItemWithNewZone.stock_disponible = newStockDisponible; // Update stock for the merged item
                    this.items.splice(itemIndex, 1); // Remove the old item
                    Livewire.dispatch('app-notification-info', { message: `Cantidades fusionadas para ${newZoneName}.` });
                } else {
                    // Otherwise, just update the zone details for the current item
                    this.items[itemIndex].zone_id = newZoneId;
                    this.items[itemIndex].zone_name = newZoneName;
                    this.items[itemIndex].stock_disponible = newStockDisponible;
                    Livewire.dispatch('app-notification-success', { message: `Zona de ${this.items[itemIndex].nombre} actualizada a ${newZoneName}.` });
                }
                // Force reactivity
                this.items = [...this.items];
            }
        },

        get count() {
            if (!this.items) return 0;
            return this.items.reduce((total, item) => total + item.quantity, 0);
        },

        get total() {
            if (!this.items) return '0.00';
            return this.items.reduce((total, item) => total + (item.precio * item.quantity), 0).toFixed(2);
        },

        clear() {
            this.items = [];
            Livewire.dispatch('app-notification-info', { message: 'Carrito limpiado' });
        }
    });
});

