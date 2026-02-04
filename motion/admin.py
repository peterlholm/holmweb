"admin module for motion"
from django.contrib import admin
from .models import Weight, BloodPressure


@admin.register(Weight)
class WeightAdmin(admin.ModelAdmin):
    "Weight admin display"
    list_display = ('date_time', 'weight', 'fat', 'muscle', 'bmi')
    list_filter = ('date',)
    search_fields = ('date',)
    ordering = ('-date',)
    readonly_fields = ('bmi',)
    fieldsets = (
        ('General Information', {
            'fields': ('person_id', 'date', 'date_time', 'gender', 'age', 'height')
        }),
        ('Body Composition', {
            'fields': ('weight', 'fat', 'muscle', 'bone', 'vfat', 'moisture', 'bmi', 'calorie')
        }),
        ('Calculated Fields', {
            'fields': ('muscle_p',),
            'classes': ('collapse',)
        }),
    )


@admin.register(BloodPressure)
class BloodPressureAdmin(admin.ModelAdmin):
    "blodtryk admin"
    list_display = ('date', 'systolic', 'diastolic', 'get_bp_status')
    list_filter = ('date',)
    search_fields = ('date',)
    ordering = ('-date',)

    def get_bp_status(self, obj):
        """Display blood pressure status"""
        if obj.systolic < 120 and obj.diastolic < 80:
            return "Normal"
        if obj.systolic < 130 and obj.diastolic < 80:
            return "Elevated"
        if obj.systolic < 140 or obj.diastolic < 90:
            return "Stage 1 Hypertension"
        return "Stage 2 Hypertension"
    get_bp_status.short_description = "Status"
