
# Proyecto prueba tecnica   

Manual de instalación del proyecto prueba tecnica +1.

---

## 💬 Introducción  
En este manual se describen los pasos necesarios para instalar el proyecto desde cero, incluyendo las distintas partes que lo conforman. Este documento está diseñado para garantizar una instalación sin problemas.  

---

## 🔧 Requisitos mínimos  
Asegúrate de cumplir con los siguientes requisitos antes de comenzar la instalación:  

- **PHP**: Versión 7.0.

---

## 📥 Clonar el repositorio  
Primero, clona el repositorio:  

```bash
git clone https://github.com/JaimeC-coder/my-jokes_prueba_tecnica.git
```  

---

## 🚀 Módulo del Proyecto  



### Servicio Backend - Laravel  
Pasos para instalar y ejecutar el servicio backend hecho en PHP con Laravel:  

#### Instalación  
1. Dirígete al directorio del proyecto:  

   ```bash
   cd my-jokes_prueba_tecnica
   ```  

2. Instala las dependencias del proyecto:  

   ```bash
   composer install
   ```  

3. Crea el archivo `.env` basado en el ejemplo:  

   ```bash
   copy .env.example .env
   ```  

4. Genera la clave de la aplicación:  

   ```bash
   php artisan key:generate
   ```  

5. Ejecuta las migraciones con sus respectivos seeders:  

   ```bash
   php artisan migrate --seed
   ```  

6. Inicia el servidor de desarrollo:  

   ```bash
   php artisan serve --port=8500
   ```  

7. Accede al backend en: [http://localhost:8500](http://localhost:8500)  
 
  
## 📧 Feedback  
Si tienes comentarios o dudas, no dudes en contactarnos:  
- **Email**: [centurionjaime@gmail.com](mailto:centurionjaime@gmail.com)  

---

## 👥 Autores  
- [@JaimeC-coder](https://github.com/JaimeC-coder)  

---


