// utils/cartUtils.ts
export function getCartItemId(productId: string, attributes: Record<string, string>) {
    // Create a string that uniquely identifies the combination
    return productId + '-' + Object.entries(attributes).sort().map(([k, v]) => `${k}:${v}`).join('|');
}