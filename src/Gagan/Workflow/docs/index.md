#Get all products

### Method
Get
### end point
/api/admin/products
### **description**
return all products belongs to user if role seller, all reviewing product if admin, all 

#Create Product
### Method
Post

### end point
/api/admin/products/create
### **description**
create a product
### Body Parameters
       name: string
       price: float
       description: string
       seoTitle: string
       seoTitle: string
       seoKeywords: string
       stock: integer
       seoDescription: string
#Update Product
### Method
Post
### end point
/api/admin/products/{productId}/update
### **description**
update a product
### Body Parameters
       name: string
       price: float
       description: string
       seoTitle: string
       seoTitle: string
       seoKeywords: string
       stock: integer
       seoDescription: string
# Transistions Product
### Method
Post
### end point
/api/admin/products/{productId}/transitions
### **description**
update a product
### Body Parameters
       status: [ACCEPT,REJECT] required
