<script setup>
const route = useRoute()
useRouter().push({
  name: 'forms-slug-show-submissions',
  params: {
    slug: route.params.slug
  }
})
</script>
