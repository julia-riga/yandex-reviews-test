<template>
    <div class="company-detail">
        <button @click="$router.back()" class="btn-back">← Назад</button>
        <div v-if="loading">Загрузка...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        <div v-else-if="company">
            <h1>{{ company.name }}</h1>
            <div class="rating">
                ⭐ {{ company.rating }} ({{ company.review_count }} отзывов,
                {{ company.rating_count }} оценок)
            </div>

            <h2>Отзывы</h2>
            <div v-if="reviews && reviews.length === 0">Нет отзывов</div>
            <div v-else>
                <div
                    v-for="review in reviews"
                    :key="review.id"
                    class="review-card"
                >
                    <div class="review-header">
                        <strong>{{ review.author }}</strong>
                        <span class="date">{{ review.date }}</span>
                        <span class="rating-stars">⭐ {{ review.rating }}</span>
                    </div>
                    <p>{{ review.text }}</p>
                </div>

                <div
                    v-if="paginationLinks && paginationLinks.length > 0"
                    class="pagination"
                >
                    <button
                        v-for="link in paginationLinks"
                        :key="link.label"
                        @click="changePage(link.url)"
                        :disabled="!link.url || link.active"
                        v-html="link.label"
                    ></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
const company = ref(null);
const reviews = ref([]);
const paginationLinks = ref([]);
const loading = ref(false);
const error = ref("");

const api = axios.create({
    baseURL: "/api",
    headers: { "Content-Type": "application/json" },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

const loadCompany = async (id) => {
    loading.value = true;
    error.value = "";
    try {
        const response = await api.get(`/companies/${id}`);
        const data = response.data;
        company.value = data;
        if (data.reviews) {
            reviews.value = data.reviews.data || [];
            paginationLinks.value = data.reviews.links || [];
        } else {
            reviews.value = [];
            paginationLinks.value = [];
        }
    } catch (err) {
        error.value =
            err.response?.data?.error || "Не удалось загрузить компанию";
    } finally {
        loading.value = false;
    }
};

const changePage = (url) => {
    if (!url) return;
    loading.value = true;
    api.get(url)
        .then((response) => {
            const data = response.data;
            if (data.reviews) {
                reviews.value = data.reviews.data || [];
                paginationLinks.value = data.reviews.links || [];
            } else if (data.data) {
                reviews.value = data.data || [];
                paginationLinks.value = data.links || [];
            }
        })
        .catch((err) => {
            error.value = "Не удалось загрузить страницу отзывов";
        })
        .finally(() => (loading.value = false));
};

onMounted(() => {
    const id = route.params.id;
    if (id) loadCompany(id);
});

watch(
    () => route.params.id,
    (newId) => {
        if (newId) loadCompany(newId);
    },
);
</script>

<style scoped>
.company-detail {
    max-width: 900px;
    margin: 0 auto;
}
.loading {
    text-align: center;
    padding: 40px;
    font-size: 1.2em;
}
.btn-back {
    background: none;
    border: none;
    color: #2196f3;
    cursor: pointer;
    font-size: 1em;
    margin-bottom: 20px;
}
.rating {
    font-size: 1.2em;
    margin-bottom: 20px;
}
.review-card {
    border-bottom: 1px solid #ddd;
    padding: 15px 0;
}
.review-header {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}
.date {
    color: #888;
    font-size: 0.9em;
}
.rating-stars {
    font-weight: bold;
    color: #f9a825;
}
.pagination {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
    margin-top: 20px;
}
.pagination button {
    padding: 5px 12px;
    border: 1px solid #ccc;
    background: #fff;
    cursor: pointer;
}
.pagination button:disabled {
    opacity: 0.5;
    cursor: default;
    background: #eee;
}
.error {
    color: #f44336;
    background: #ffebee;
    padding: 15px;
    border-radius: 4px;
    margin: 20px 0;
}
</style>
